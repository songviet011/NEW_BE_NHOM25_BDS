<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoiTin extends Model
{
    protected $table = 'goi_tins';

    protected $fillable = [
        'ten_goi',
        'mo_ta',
        'gia',
        'so_ngay',
        'so_luong_tin',
        'gan_nhan_vip',
        'uu_tien_hien_thi',
        'trang_thai',
    ];

    protected $casts = [
        'gia' => 'decimal:0',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'moi_gioi_id');
    }

    public function goiTin()
    {
        return $this->belongsTo(GoiTin::class, 'goi_tin_id');
    }
}
