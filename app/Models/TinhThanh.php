<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TinhThanh extends Model
{
    protected $table = 'tinh_thanhs';

    protected $fillable = [
        'ten',
    ];

    protected $casts = [
        //
    ];

    public function quanHuyens(): HasMany
    {
        return $this->hasMany(QuanHuyen::class, 'tinh_id');
    }

    public function batDongSans(): HasMany
    {
        return $this->hasMany(BatDongSan::class, 'tinh_id');
    }

    public function diaChis(): HasMany
    {
        return $this->hasMany(DiaChi::class, 'tinh_id');
    }
}
