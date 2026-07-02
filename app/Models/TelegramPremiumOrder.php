<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramPremiumOrder extends Model
{
    protected $fillable = [
        'user_id',
        'istar_order_id',
        'ref_id',
        'username',
        'recipient_hash',
        'recipient_name',
        'months',
        'amount_ngn',
        'amount_usd',
        'status',
        'tx_hash',
        'failure_reason',
        'provider_response',
        'completed_at',
    ];

    protected $casts = [
        'months' => 'integer',
        'amount_ngn' => 'float',
        'amount_usd' => 'float',
        'provider_response' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
