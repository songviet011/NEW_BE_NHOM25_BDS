<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuanHuyen extends Model
{
    protected $table = 'quan_huyens';

    protected $fillable = [
        'ten',
        'tinh_id',
    ];

    protected $casts = [
        //
    ];

    public function tinh(): BelongsTo
    {
        return $this->belongsTo(TinhThanh::class, 'tinh_id');
    }

    public function batDongSans(): HasMany
    {
        return $this->hasMany(BatDongSan::class, 'quan_id');
    }
}
