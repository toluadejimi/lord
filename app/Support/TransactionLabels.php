<?php

namespace App\Support;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;

class TransactionLabels
{
    public const FILTER_ALL = 'all';

    public const FILTER_VERIFICATION = 'verification';

    public const FILTER_VTU = 'vtu';

    public const FILTER_API = 'api';

  /** @return array<string, string> */
    public static function adminFilters(): array
    {
        return [
            self::FILTER_ALL => 'All',
            self::FILTER_VERIFICATION => 'Verifications',
            self::FILTER_VTU => 'VTU & Bills',
            self::FILTER_API => 'API Funding',
        ];
    }

    public static function categoryFor(Transaction $transaction): string
    {
        $ref = (string) $transaction->ref_id;
        $type = (int) $transaction->type;

        if (self::refMatchesApiFunding($ref)) {
            return self::FILTER_API;
        }

        if ($type === 4 || self::refMatchesVtu($ref)) {
            return self::FILTER_VTU;
        }

        if (self::refMatchesVerification($ref, $type)) {
            return self::FILTER_VERIFICATION;
        }

        return 'other';
    }

    public static function categoryLabel(string $category): string
    {
        return self::adminFilters()[$category] ?? match ($category) {
            'other' => 'Other',
            default => 'Other',
        };
    }

    /**
     * @return array{0: string, 1: string}
     */
    public static function categoryBadge(string $category): array
    {
        return match ($category) {
            self::FILTER_VERIFICATION => ['Verifications', 'primary'],
            self::FILTER_VTU => ['VTU & Bills', 'warning'],
            self::FILTER_API => ['API Funding', 'info'],
            default => ['Other', 'secondary'],
        };
    }

    public static function applyAdminFilter(Builder $query, string $filter): Builder
    {
        return match ($filter) {
            self::FILTER_VERIFICATION => $query->where(function (Builder $q) {
                $q->where(function (Builder $inner) {
                    $inner->where('type', 1)
                        ->where(function (Builder $refs) {
                            $refs->where('ref_id', 'like', 'SMS-%')
                                ->orWhere('ref_id', 'like', 'USA2-%');
                        });
                })->orWhere(function (Builder $inner) {
                    $inner->where('type', 3)
                        ->where('ref_id', 'like', 'REFUND-%');
                });
            }),
            self::FILTER_VTU => $query->where(function (Builder $q) {
                $q->where('type', 4)
                    ->orWhere('ref_id', 'like', 'AIR-%')
                    ->orWhere('ref_id', 'like', 'DATA-%')
                    ->orWhere('ref_id', 'like', 'TV-%')
                    ->orWhere('ref_id', 'like', 'PWR-%');
            }),
            self::FILTER_API => $query->where(function (Builder $q) {
                $q->where('ref_id', 'like', 'VERF%')
                    ->orWhere('ref_id', 'like', 'VERFM%');
            }),
            default => $query,
        };
    }

    protected static function refMatchesApiFunding(string $ref): bool
    {
        return str_starts_with($ref, 'VERF');
    }

    protected static function refMatchesVtu(string $ref): bool
    {
        foreach (['AIR-', 'DATA-', 'TV-', 'PWR-'] as $prefix) {
            if (str_starts_with($ref, $prefix)) {
                return true;
            }
        }

        return false;
    }

    protected static function refMatchesVerification(string $ref, int $type): bool
    {
        if ($type === 3 && str_starts_with($ref, 'REFUND-')) {
            return true;
        }

        if ($type === 1 && (str_starts_with($ref, 'SMS-') || str_starts_with($ref, 'USA2-'))) {
            return true;
        }

        return false;
    }

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
