<?php

namespace App\Support;

class TransactionLabels
{
    public static function typeLabel(int $type): string
    {
        return match ($type) {
            1 => 'Purchase',
            2 => 'Wallet funding',
            3 => 'Refund',
            4 => 'Bills & VTU',
            default => 'Transaction',
        };
    }

    public static function isCredit(int $type): bool
    {
        return in_array($type, [2, 3], true);
    }

    public static function direction(int $type): string
    {
        return self::isCredit($type) ? 'credit' : 'debit';
    }

    public static function directionLabel(int $type): string
    {
        return self::isCredit($type) ? 'Credit' : 'Debit';
    }

    /**
     * @return array{0: string, 1: string}
     */
    public static function statusBadge(int $status): array
    {
        return match ($status) {
            0 => ['Pending', 'secondary'],
            1 => ['Initiated', 'warning'],
            2 => ['Completed', 'success'],
            3 => ['Cancelled', 'danger'],
            4 => ['Resolved', 'info'],
            default => ['Unknown', 'secondary'],
        };
    }
}
