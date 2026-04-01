<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MoiGioi extends Authenticatable
{
    use Notifiable,HasApiTokens;

    protected $table = 'moi_giois';

    protected $fillable = [
        'ten',
        'email',
        'so_dien_thoai',
        'password',
        'avatar',
        'mo_ta',
        'zalo_link',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function batDongSans(): HasMany
    {
        return $this->hasMany(BatDongSan::class, 'moi_gioi_id');
    }

    public function giaoDichs(): HasMany
    {
        return $this->hasMany(GiaoDich::class, 'moi_gioi_id');
    }

    public function lichSuGoiTins(): HasMany
    {
        return $this->hasMany(LichSuGoiTin::class, 'moi_gioi_id');
    }

    public function yeuThichs(): HasMany
    {
        return $this->hasMany(YeuThich::class, 'moi_gioi_id');
    }
}
