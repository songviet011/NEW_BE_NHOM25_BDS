<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrangThaiBatDongSan extends Model
{
    protected $table = 'trang_thai_bat_dong_sans';

    protected $fillable = [
        'ten_trang_thai',
    ];

    protected $casts = [
        //
    ];

    public function batDongSans(): HasMany
    {
        return $this->hasMany(BatDongSan::class, 'trang_thai_id');
    }
}
