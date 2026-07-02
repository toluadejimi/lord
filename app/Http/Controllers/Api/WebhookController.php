<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TelegramPremiumOrder;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Services\AppConfigService;
use App\Services\TelegramPremiumOrderService;
use App\Services\VerificationOrderService;
use App\Services\WalletFundingService;
use App\Services\WalletService;
use App\Services\WebhookDispatchService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected AppConfigService $config,
        protected VerificationOrderService $orders,
        protected WebhookDispatchService $webhooks,
        protected WalletService $wallet,
        protected WalletFundingService $funding,
        protected TelegramPremiumOrderService $telegramPremium,
    ) {}

    protected function verifyInbound(Request $request): bool
    {
        $secret = $this->config->get('WEBHOOK_INBOUND_SECRET');
        if (!$secret) {
            return true;
        }

        return $request->header('X-Webhook-Secret') === $secret
            || $request->input('secret') === $secret;
    }

    protected function applySms(Verification $verification, string $code, ?string $fullSms = null): void
    {
        if ((int) $verification->status === 2) {
            return;
        }
        $this->orders->completeVerification($verification, $code, $fullSms ?? $code);
    }

    public function smsPool(Request $request)
    {
        if (!$this->verifyInbound($request)) {
            return response()->json(['success' => false], 403);
        }

        $orderId = $request->input('orderid') ?? $request->input('activationId');
        $code = $request->input('sms') ?? $request->input('code');
        $verification = Verification::where('order_id', $orderId)->first();
        if ($verification && $code) {
            $this->applySms($verification, (string) $code, $request->input('text'));
        }

        return response()->json(['success' => true]);
    }

    public function hero(Request $request)
    {
        if (!$this->verifyInbound($request)) {
            return response()->json(['success' => false], 403);
        }

        $orderId = $request->input('activationId') ?? $request->input('orderid');
        $code = $request->input('code') ?? $request->input('sms');
        $verification = Verification::where('order_id', $orderId)->whereIn('type', [9])->first();
        if ($verification && $code) {
            $this->applySms($verification, (string) $code, $request->input('text'));
        }

        return response()->json(['success' => true]);
    }

    public function sv3(Request $request)
    {
        if (!$this->verifyInbound($request)) {
            return response()->json(['success' => false], 403);
        }

        $orderId = $request->input('activationId') ?? $request->input('orderid');
        $code = $request->input('code') ?? $request->input('sms');
        $verification = Verification::where('order_id', $orderId)->whereIn('type', [10])->first();
        if ($verification && $code) {
            $this->applySms($verification, (string) $code, $request->input('text'));
        }

        return response()->json(['success' => true]);
    }

    public function worldLegacy(Request $request)
    {
        return $this->smsPool($request);
    }

    public function sprintPay(Request $request)
    {
        $secret = $this->config->get('SPRINTPAY_WEBHOOK_SECRET');
        $auth = $request->bearerToken();
        if ($secret && $auth !== $secret) {
            return response()->json(['success' => false], 401);
        }

        $refId = (string) ($request->input('ref_id')
            ?? $request->input('ref')
            ?? $request->input('order_id')
            ?? $request->input('trx_ref')
            ?? '');
        $email = $request->input('email');
        $amount = (float) ($request->input('amount') ?? $request->input('trx') ?? 0);

        if ($refId === '' || !$email || $amount <= 0) {
            return response()->json(['success' => false, 'message' => 'Invalid payload'], 422);
        }

        if (Transaction::where('ref_id', $refId)->where('status', 2)->exists()) {
            return response()->json(['success' => true, 'message' => 'Already credited']);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $txn = $this->funding->completePendingFunding($user, $refId, $amount);

        return response()->json([
            'success' => true,
            'message' => 'Deposit recorded',
            'ref_id' => $txn->ref_id,
            'amount' => (float) $txn->amount,
        ]);
    }

    public function iStar(Request $request)
    {
        $secret = $this->config->get('ISTAR_WEBHOOK_SECRET');
        if ($secret) {
            $body = $request->getContent();
            $signature = $request->header('X-iStar-Signature') ?? '';
            $expected = hash_hmac('sha256', $body, $secret);
            if (!hash_equals($expected, $signature)) {
                return response()->json(['success' => false], 401);
            }
        }

        $eventType = $request->input('event_type') ?? $request->header('X-iStar-Event');
        $orderPayload = $request->input('order', []);
        $istarOrderId = (string) ($orderPayload['id'] ?? $request->input('order_id') ?? '');

        if ($istarOrderId === '') {
            return response()->json(['success' => false, 'message' => 'Missing order id'], 422);
        }

        $order = TelegramPremiumOrder::where('istar_order_id', $istarOrderId)->first();
        if (!$order) {
            return response()->json(['success' => true, 'message' => 'Order not tracked']);
        }

        if ($eventType === 'order.completed') {
            $txHash = $request->input('tx_hash')
                ?? ($orderPayload['payload']['tx_hash'] ?? null);
            $this->telegramPremium->markCompleted($order, $txHash ? (string) $txHash : null);
        } elseif ($eventType === 'order.failed') {
            $reason = (string) ($request->input('error')
                ?? $orderPayload['payload']['reason']
                ?? 'Order failed at provider');
            $this->telegramPremium->markFailed($order, $reason, true);
        }

        return response()->json(['success' => true]);
    }
}
