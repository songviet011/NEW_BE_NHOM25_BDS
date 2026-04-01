<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChucNang extends Model
{
    protected $table = 'chuc_nangs';

    protected $fillable = [
        'ten_chuc_nang',
        'url_chuc_nang',
        'methods_chuc_nang',
        'mo_ta_chuc_nang',
    ];

    protected $casts = [
        //
    ];
}
