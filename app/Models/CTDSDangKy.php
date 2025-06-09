<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class CTDSDangKy extends Model
{
    use HasFactory;

    protected $table = 'c_t_d_s_dang_kies';

    protected $fillable = [
        'id',
        'ten',
        'id_ds_dang_ky',
        'id_hoc_phan',
        'trang_thai',
        'able',
        'loai_ngan_hang',
        'so_luong',
        'hinh_thuc_thi'
    ];

    public function dsDangKy()
    {
        return $this->belongsTo(DSDangKy::class, 'id_ds_dang_ky');
    }

    public function hocPhan()
    {
        return $this->belongsTo(HocPhan::class, 'id_hoc_phan');
    }

    public function dsGVBienSoans()
    {
        return $this->hasMany(DSGVBienSoan::class, 'id_ct_ds_dang_ky');
    }
    
    // Phương thức tiện ích để lấy các viên chức biên soạn
    public function vienChucs()
    {
        return User::whereIn('id', $this->dsGVBienSoans->pluck('id_vien_chuc'))->get();
    }

    public function cauHois()
    {
        return $this->hasMany(CauHoi::class, 'id_ct_ds_dang_ky');
    }

    public function bienBanHop()
    {
        return $this->hasMany(BienBanHop::class, 'id_ct_ds_dang_ky');
    }

    public function deThis()
    {
        return $this->hasMany(DeThi::class, 'id_ct_ds_dang_ky');
    }

}
