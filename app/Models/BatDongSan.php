<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatDongSan extends Model
{
    protected $table = 'bat_dong_sans';

    protected $fillable = [
        'tieu_de',
        'mo_ta',
        'gia',
        'dien_tich',
        'loai_id',
        'trang_thai_id',
        'moi_gioi_id',
        'tinh_id',
        'quan_id',
        'dia_chi_id',
        'so_phong_ngu',
        'so_phong_tam',
        'is_duyet',
        'is_noi_bat',
    ];

    protected $casts = [
        'gia' => 'decimal:0',
        'dien_tich' => 'float',
        'is_duyet' => 'boolean',
        'is_noi_bat' => 'boolean',
    ];

    public function loai(): BelongsTo
    {
        return $this->belongsTo(LoaiBatDongSan::class, 'loai_id');
    }

    public function trangThai(): BelongsTo
    {
        return $this->belongsTo(TrangThaiBatDongSan::class, 'trang_thai_id');
    }

    public function moiGioi(): BelongsTo
    {
        return $this->belongsTo(MoiGioi::class, 'moi_gioi_id');
    }

    public function tinh(): BelongsTo
    {
        return $this->belongsTo(TinhThanh::class, 'tinh_id');
    }

    public function quan(): BelongsTo
    {
        return $this->belongsTo(QuanHuyen::class, 'quan_id');
    }

    public function diaChi(): BelongsTo
    {
        return $this->belongsTo(DiaChi::class, 'dia_chi_id');
    }

    public function hinhAnh(): HasMany
    {
        return $this->hasMany(HinhAnhBatDongSan::class, 'bds_id');
    }
}
