<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiaChi extends Model
{
    protected $table = 'dia_chis';

    protected $fillable = [
        'tinh_id',
        'quan_id',
        'dia_chi_chi_tiet',
        'lat',
        'lng',
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    public function tinh(): BelongsTo
    {
        return $this->belongsTo(TinhThanh::class, 'tinh_id');
    }

    public function quan(): BelongsTo
    {
        return $this->belongsTo(QuanHuyen::class, 'quan_id');
    }

    public function batDongSans(): HasMany
    {
        return $this->hasMany(BatDongSan::class, 'dia_chi_id');
    }
}
