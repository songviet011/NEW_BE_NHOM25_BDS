<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KhachHang extends Authenticatable
{
    use Notifiable;

    protected $table = 'khach_hangs';

    protected $fillable = [
        'ten',
        'email',
        'so_dien_thoai',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function yeuThichs(): HasMany
    {
        return $this->hasMany(YeuThich::class, 'khach_hang_id');
    }
}
