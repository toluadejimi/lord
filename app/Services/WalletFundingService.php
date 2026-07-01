<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Services\Payment\SprintPayClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WalletFundingService
{
    public function __construct(
        protected AppConfigService $config,
        protected SprintPayClient $sprintPay,
    ) {}

    public function isSuccessStatus(mixed $status): bool
    {
        if ($status === true || $status === 1) {
            return true;
        }

        if (!is_string($status) && !is_numeric($status)) {
            return false;
        }

        return in_array(strtolower((string) $status), [
            'success',
            'successful',
            'completed',
            'complete',
            'paid',
            'true',
            '1',
            '200',
            'approved',
        ], true);
    }

    public function isFailureStatus(mixed $status): bool
    {
        if ($status === false || $status === 0) {
            return true;
        }

        if (!is_string($status) && !is_numeric($status)) {
            return false;
        }

        return in_array(strtolower((string) $status), [
            'failed',
            'failure',
            'cancel',
            'cancelled',
            'canceled',
            'false',
            '0',
            'abandoned',
        ], true);
    }

    public function findLatestPendingFunding(User $user): ?Transaction
    {
        return Transaction::where('user_id', $user->id)
            ->where('status', 1)
            ->where('type', 2)
            ->where('ref_id', 'like', 'VERF%')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * @return array{success: bool, amount: float, message: ?string}
     */
    public function resolveWithProvider(string $ref, ?string $sessionId = null): array
    {
        $pending = Transaction::where('ref_id', $ref)->where('status', 1)->first();
        $fallbackAmount = (float) ($pending?->amount ?? 0);

        if ($sessionId) {
            $result = $this->parseResolveResponse($this->sprintPay->resolve($sessionId, $ref), $fallbackAmount);
            if ($result['success']) {
                return $result;
            }
        }

        try {
            $response = Http::timeout(30)
                ->asForm()
                ->post(rtrim($this->config->get('SPRINTPAY_API_BASE', 'https://web.sprintpay.online/api'), '/').'/resolve', array_filter([
                    'ref' => $ref,
                    'key' => $this->config->get('WEBKEY', ''),
                ]));
            $result = $this->parseResolveResponse($response->json() ?? [], $fallbackAmount);
            if ($result['success']) {
                return $result;
            }
        } catch (\Throwable) {
            // fall through to resolve-complete
        }

        if ($this->sprintPay->resolveComplete($ref)) {
            $amount = $fallbackAmount;
            if ($amount > 0) {
                return ['success' => true, 'amount' => $amount, 'message' => null];
            }
        }

        return ['success' => false, 'amount' => 0.0, 'message' => 'Payment could not be verified yet.'];
    }

    /**
     * @param  array<string, mixed>|null  $json
     * @return array{success: bool, amount: float, message: ?string}
     */
    protected function parseResolveResponse(?array $json, float $fallbackAmount = 0): array
    {
        if (!is_array($json)) {
            return ['success' => false, 'amount' => 0.0, 'message' => 'Invalid response from payment provider.'];
        }

        $status = $json['status'] ?? null;
        $amount = (float) ($json['amount'] ?? $json['trx'] ?? $json['data']['amount'] ?? 0);
        $message = isset($json['message']) ? (string) $json['message'] : null;
        $success = $this->isSuccessStatus($status) || $status === true;

        if ($success && $amount <= 0 && $fallbackAmount > 0) {
            $amount = $fallbackAmount;
        }

        return [
            'success' => $success && $amount > 0,
            'amount' => $amount,
            'message' => $message,
        ];
    }

    public function completePendingFunding(User $user, string $refId, float $amount): Transaction
    {
        return DB::transaction(function () use ($user, $refId, $amount) {
            $txn = Transaction::where('ref_id', $refId)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($txn && (int) $txn->status === 2) {
                return $txn;
            }

            $locked = User::where('id', $user->id)->lockForUpdate()->firstOrFail();
            $oldBalance = (float) $locked->wallet;

            if ($txn && (int) $txn->status === 1) {
                $locked->increment('wallet', $amount);
                $txn->update([
                    'amount' => $amount,
                    'status' => 2,
                    'old_balance' => $oldBalance,
                    'balance' => $oldBalance + $amount,
                ]);

                return $txn->fresh();
            }

            $locked->increment('wallet', $amount);

            return Transaction::create([
                'user_id' => $locked->id,
                'amount' => $amount,
                'ref_id' => $refId,
                'status' => 2,
                'type' => 2,
                'old_balance' => $oldBalance,
                'balance' => $oldBalance + $amount,
            ]);
        });
    }

    public function extractRefFromRequest(array $input): ?string
    {
        foreach (['ref', 'trx_ref', 'reference', 'ref_id', 'order_id', 'orderid'] as $key) {
            $value = $input[$key] ?? null;
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    public function extractSessionIdFromRequest(array $input): ?string
    {
        foreach (['session_id', 'sessionId', 'session'] as $key) {
            $value = $input[$key] ?? null;
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }
}
