<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'ten',
        'email',
        'password',
        'is_super',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_super' => 'boolean',
    ];
}
