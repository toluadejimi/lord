<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationSms extends Model
{
    protected $fillable = ['verification_id', 'sms'];

    public function verification()
    {
        return $this->belongsTo(Verification::class);
    }
}
