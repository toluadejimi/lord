<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function credit(User $user, float $amount, string $refId, int $type = 2): Transaction
    {
        return DB::transaction(function () use ($user, $amount, $refId, $type) {
            if (Transaction::where('ref_id', $refId)->where('status', 2)->exists()) {
                return Transaction::where('ref_id', $refId)->first();
            }

            $locked = User::where('id', $user->id)->lockForUpdate()->first();
            $oldBalance = (float) $locked->wallet;
            $locked->increment('wallet', $amount);

            $txn = Transaction::create([
                'user_id' => $locked->id,
                'amount' => $amount,
                'ref_id' => $refId,
                'status' => 2,
                'type' => $type,
                'old_balance' => $oldBalance,
                'balance' => $oldBalance + $amount,
            ]);

            return $txn;
        });
    }

    public function debit(User $user, float $amount, string $refId, int $type = 1): ?Transaction
    {
        return DB::transaction(function () use ($user, $amount, $refId, $type) {
            $locked = User::where('id', $user->id)->lockForUpdate()->first();
            if ((float) $locked->wallet < $amount) {
                return null;
            }

            $oldBalance = (float) $locked->wallet;
            $locked->decrement('wallet', $amount);

            return Transaction::create([
                'user_id' => $locked->id,
                'amount' => $amount,
                'ref_id' => $refId,
                'status' => 2,
                'type' => $type,
                'old_balance' => $oldBalance,
                'balance' => $oldBalance - $amount,
            ]);
        });
    }

    public function refund(User $user, float $amount, string $refId): Transaction
    {
        return $this->credit($user, $amount, $refId, 3);
    }
}
