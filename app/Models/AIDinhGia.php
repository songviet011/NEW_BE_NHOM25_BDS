<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIDinhGia extends Model
{
    protected $table = 'a_i_dinh_gias';

    protected $fillable = [
        'bat_dong_san_id',
        'gia_du_doan',
        'do_tin_cay',
        'ly_do',
    ];

    protected $casts = [
        'gia_du_doan' => 'decimal:0',
        'do_tin_cay' => 'float',
    ];

    public function batDongSan(): BelongsTo
    {
        return $this->belongsTo(BatDongSan::class, 'bat_dong_san_id');
    }
}
