<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'order_id',
        'country',
        'service',
        'cost',
        'api_cost',
        'status',
        'type',
        'ip',
        'expires_in',
        'sms',
        'full_sms',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
