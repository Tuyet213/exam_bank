<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class BienBanHop extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'loai',
        'dia_diem',
        'noi_dung',
        'thoi_gian',
        'id_ct_ds_dang_ky',
    ];

    public function ctDSDangKy()
    {
        return $this->belongsTo(CTDSDangKy::class, 'id_ct_ds_dang_ky');
    }


    public function dsHop()
    {
        return $this->hasMany(DSHop::class, 'id_bien_ban_hop');
    }
    
}
