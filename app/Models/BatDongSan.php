<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


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
        'dia_chi_id',
        'so_phong_ngu',
        'so_phong_tam',
        'is_duyet',
        'is_noi_bat',
        'expires_at',
    ];

    protected $casts = [
        'gia' => 'decimal:0',
        'dien_tich' => 'float',
        'is_duyet' => 'boolean',
        'is_noi_bat' => 'boolean',
    ];

    protected $appends = ['anh_dai_dien_url', 'is_expired'];

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
    public function diaChi(): BelongsTo
    {
        return $this->belongsTo(DiaChi::class, 'dia_chi_id');
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }
    public function hinhAnh()
    {
        return $this->hasMany(HinhAnhBatDongSan::class, 'bds_id')->orderBy('thu_tu', 'asc');
    }

    public function anhDaiDien()
    {
        return $this->hasOne(HinhAnhBatDongSan::class, 'bds_id')
            ->where('is_anh_dai_dien', true)
            ->orderBy('thu_tu', 'asc');
    }

    public function getAnhDaiDienUrlAttribute()
    {
        $anh = $this->anhDaiDien;

        if ($anh) {
            if (filter_var($anh->url, FILTER_VALIDATE_URL)) {
                return $anh->url; // link online
            }

            return asset('storage/' . $anh->url); // ảnh upload local
        }

        $first = $this->hinhAnh->first();

        if ($first) {
            if (filter_var($first->url, FILTER_VALIDATE_URL)) {
                return $first->url;
            }

            return asset('storage/' . $first->url);
        }

        return null;
    }
}
