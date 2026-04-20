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
        'paid_at',
        'ma_giao_dich',
        'ma_sepay_txn_ref',
    ];

    protected $casts = [
        'so_tien' => 'decimal:0',
        'created_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED  = 'failed';

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
