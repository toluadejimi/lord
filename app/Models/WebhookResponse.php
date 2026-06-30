<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookResponse extends Model
{
    protected $fillable = ['order_id', 'response_code', 'response_body', 'url'];
}
