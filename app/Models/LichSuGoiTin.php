<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LichSuGoiTin extends Model
{

    protected $table = 'lich_su_goi_tins';

    protected $fillable = [
        'moi_gioi_id',
        'goi_tin_id',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai'
    ];

    public function moiGioi()
    {
        return $this->belongsTo(MoiGioi::class, 'moi_gioi_id');
    }

    public function goiTin()
    {
        return $this->belongsTo(GoiTin::class, 'goi_tin_id');
    }
}
