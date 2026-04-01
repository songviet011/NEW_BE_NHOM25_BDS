<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use Notifiable,HasApiTokens;

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
