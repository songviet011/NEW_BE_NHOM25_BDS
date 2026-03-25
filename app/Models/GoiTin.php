<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoiTin extends Model
{
    protected $table = 'goi_tins';

    protected $fillable = [
        'ten_goi',
        'gia',
        'so_ngay',
        'so_luong_tin',
    ];

    protected $casts = [
        'gia' => 'decimal:0',
    ];

    public function giaoDichs(): HasMany
    {
        return $this->hasMany(GiaoDich::class, 'goi_tin_id');
    }

    public function lichSuGoiTins(): HasMany
    {
        return $this->hasMany(LichSuGoiTin::class, 'goi_tin_id');
    }
}
