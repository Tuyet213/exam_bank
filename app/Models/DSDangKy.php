<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class DSDangKy extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'ten',
        'id_bo_mon',
        'thoi_gian',
        'able'
    ];

  

    public function boMon()
    {
        return $this->belongsTo(BoMon::class, 'id_bo_mon');
    }

    public function ctDSDangKies()
    {
        return $this->hasMany(CTDSDangKy::class, 'id_ds_dang_ky');
    }
}
