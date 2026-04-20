<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoaiBatDongSan extends Model
{
    protected $table = 'loai_bat_dong_sans';

    protected $fillable = [
        'ten_loai',
        'is_active',
    ];

    protected $casts = [
        //
    ];

    public function batDongSans(): HasMany
    {
        return $this->hasMany(BatDongSan::class, 'loai_id');
    }
}
