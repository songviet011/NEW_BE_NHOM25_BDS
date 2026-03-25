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
    ];

    protected $casts = [
        'so_tien' => 'decimal:0',
    ];

    public function moiGioi(): BelongsTo
    {
        return $this->belongsTo(MoiGioi::class);
    }

    public function goiTin(): BelongsTo
    {
        return $this->belongsTo(GoiTin::class, 'goi_tin_id');
    }
}
