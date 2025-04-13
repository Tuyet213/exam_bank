<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class CTDSDangKy extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'ten',
        'id_ds_dang_ky',
        'id_hoc_phan',
        'id_vien_chuc',
        'so_gio',
        'trang_thai',
        'able',
        'loai_ngan_hang',
        'so_luong'
    ];

    public function dsDangKy()
    {
        return $this->belongsTo(DSDangKy::class, 'id_ds_dang_ky');
    }

    public function hocPhan()
    {
        return $this->belongsTo(HocPhan::class, 'id_hoc_phan');
    }

    public function vienChuc()
    {
        return $this->belongsTo(User::class, 'id_vien_chuc');
    }
    

    public function cauHois()
    {
        return $this->hasMany(CauHoi::class, 'id_ct_ds_dang_ky');
    }

    public function bienBanHop()
    {
        return $this->hasMany(BienBanHop::class, 'id_ct_ds_dang_ky');
    }

}
