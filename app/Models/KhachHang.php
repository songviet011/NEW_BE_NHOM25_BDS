<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class KhachHang extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table = 'khach_hangs';

    protected $fillable = [
        'ten',
        'email',
        'so_dien_thoai',
        'password',
        'is_active',
        'trang_thai',
        'hash_reset',
        'hash_reset_expires_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function yeuThichs(): HasMany
    {
        return $this->hasMany(YeuThich::class, 'khach_hang_id');
    }
}
