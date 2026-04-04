<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YeuThich extends Model
{
    protected $table = 'yeu_thichs';

    protected $fillable = [
        'moi_gioi_id',
        'khach_hang_id',
        'bds_id',
        'noi_dung',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function moiGioi(): BelongsTo
    {
        return $this->belongsTo(MoiGioi::class);
    }
    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    }

    public function batDongSan()
    {
        return $this->belongsTo(BatDongSan::class, 'bds_id');
    }
}
