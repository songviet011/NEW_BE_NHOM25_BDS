<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HinhAnhBatDongSan extends Model
{
    protected $table = 'hinh_anh_bat_dong_sans';

    protected $fillable = [
        'bds_id',
        'url',
    ];

    protected $casts = [
        //
    ];

    public function batDongSan(): BelongsTo
    {
        return $this->belongsTo(BatDongSan::class, 'bds_id');
    }
}
