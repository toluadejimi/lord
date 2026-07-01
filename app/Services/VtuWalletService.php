<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VtuWalletService
{
    /**
     * @return array{old_balance: float, new_balance: float}|null
     */
    public function tryDebitForVas(int $userId, float $amount): ?array
    {
        return DB::transaction(function () use ($userId, $amount) {
            $user = User::where('id', $userId)->lockForUpdate()->first();

            if (!$user || (float) $user->wallet < $amount) {
                return null;
            }

            $oldBalance = (float) $user->wallet;
            $user->decrement('wallet', $amount);
            $newBalance = $oldBalance - $amount;

            $check = DB::table('wallet_checks')->where('user_id', $userId)->lockForUpdate()->first();

            if ($check) {
                DB::table('wallet_checks')->where('user_id', $userId)->update([
                    'total_bought' => (float) $check->total_bought + $amount,
                    'wallet_amount' => $newBalance,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('wallet_checks')->insert([
                    'user_id' => $userId,
                    'total_funded' => 0,
                    'wallet_amount' => $newBalance,
                    'total_bought' => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return [
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
            ];
        });
    }

    public function refundVas(int $userId, float $amount): void
    {
        DB::transaction(function () use ($userId, $amount) {
            $user = User::where('id', $userId)->lockForUpdate()->first();

            if (!$user) {
                return;
            }

            $user->increment('wallet', $amount);
            $newBalance = (float) $user->fresh()->wallet;

            $check = DB::table('wallet_checks')->where('user_id', $userId)->lockForUpdate()->first();

            if ($check) {
                DB::table('wallet_checks')->where('user_id', $userId)->update([
                    'total_bought' => max(0, (float) $check->total_bought - $amount),
                    'wallet_amount' => $newBalance,
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function recordVasTransaction(
        int $userId,
        float $amount,
        float $oldBalance,
        float $newBalance,
        string $prefix,
    ): Transaction {
        return Transaction::create([
            'user_id' => $userId,
            'amount' => $amount,
            'ref_id' => $prefix.Str::lower(Str::random(12)),
            'status' => 2,
            'type' => 4,
            'old_balance' => $oldBalance,
            'balance' => $newBalance,
        ]);
    }
}
