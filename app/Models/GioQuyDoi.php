<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class GioQuyDoi extends Model
{
    use HasFactory;

    protected $fillable = [
        'loai_de_thi',
        'loai_hanh_dong',
        'gio',
        'so_luong',
        'able'
    ];

    
}
