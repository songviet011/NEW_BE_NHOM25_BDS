<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'khach_hang_id',
        'moi_gioi_id',
        'bat_dong_san_id',
        'last_message_id',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }

    public function khachHang(): BelongsTo
    {
        return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    }

    public function moiGioi(): BelongsTo
    {
        return $this->belongsTo(MoiGioi::class, 'moi_gioi_id');
    }

    public function batDongSan(): BelongsTo
    {
        return $this->belongsTo(BatDongSan::class, 'bat_dong_san_id');
    }

    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }
}
