<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiaoDich extends Model
{
    protected $table = 'giao_dichs';

    protected $fillable = [
        'moi_gioi_id',
        'goi_tin_id',
        'so_tien',
        'phuong_thuc',
        'trang_thai',
        'ma_giao_dich',
        'ma_vnp_txn_ref',
    ];

    protected $casts = [
        'so_tien' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // Quan hệ (Relationship)
    public function moiGioi()
    {
        return $this->belongsTo(MoiGioi::class, 'moi_gioi_id');
    }

    public function goiTin(): BelongsTo
    {
        return $this->belongsTo(GoiTin::class, 'goi_tin_id');
    }
}
