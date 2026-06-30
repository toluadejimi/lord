<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = ['title', 'message', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
