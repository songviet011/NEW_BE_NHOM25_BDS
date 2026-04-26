<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnmatchedPayment extends Model
{
    protected $fillable = [
        'sepayer_reference',
        'order_code_from_sepay',
        'so_tien',
        'payload',
        'status',
        'giao_dich_id',
        'admin_notes',
    ];

    protected $casts = [
        'payload' => 'array',
        'so_tien' => 'decimal:2',
    ];

    public function giaoDich()
    {
        return $this->belongsTo(GiaoDich::class);
    }
}