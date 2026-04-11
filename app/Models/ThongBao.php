<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThongBao extends Model
{
    protected $table = 'thong_baos';

    protected $fillable = [
        'moi_gioi_id',
        'khach_hang_id',
        'bat_dong_san_id',
        'tieu_de',
        'noi_dung',
        'trang_thai',
    ];

    protected $casts = [
        'trang_thai' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Quan hệ: Thông báo thuộc về Môi giới
    public function moiGioi(): BelongsTo
    {
        return $this->belongsTo(MoiGioi::class, 'moi_gioi_id');
    }

    // Quan hệ: Thông báo được tạo bởi Khách hàng
    public function khachHang(): BelongsTo
    {
        return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    }

    // Quan hệ: Thông báo liên quan đến BĐS
    public function batDongSan(): BelongsTo
    {
        return $this->belongsTo(BatDongSan::class, 'bat_dong_san_id');
    }
}