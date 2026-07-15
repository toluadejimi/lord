<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EsimOrder extends Model
{
    protected $fillable = [
        'user_id',
        'ref_id',
        'provider_order_id',
        'package_code',
        'package_name',
        'location',
        'volume_gb',
        'duration_days',
        'amount_ngn',
        'amount_usd',
        'provider_price_cents',
        'status',
        'iccid',
        'qr_code_url',
        'activation_code',
        'short_url',
        'esim_status',
        'failure_reason',
        'provider_response',
        'completed_at',
    ];

    protected $casts = [
        'volume_gb' => 'float',
        'duration_days' => 'integer',
        'amount_ngn' => 'float',
        'amount_usd' => 'float',
        'provider_price_cents' => 'integer',
        'provider_response' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
