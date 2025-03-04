<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class GioQuyDoi extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'gio',
        'tin_chi',
        'cau'
    ];

    
}
