<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class CauHoi extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'cau_hoi',
        'id_ct_ds_dang_ky',
        'id_chuan_dau_ra',
        'id_chuong',
        'diem',
        'phan_loai',
        'muc_do',
        'able'
    ];
 

    public function chuong()
    {
        return $this->belongsTo(Chuong::class, 'id_chuong');
    }

    public function chuanDauRa()
    {
        return $this->belongsTo(ChuanDauRa::class, 'id_chuan_dau_ra');
    }

    public function ctDSDangKy()
    {
        return $this->belongsTo(CTDSDangKy::class, 'id_ct_ds_dang_ky');
    }

    public function deThi()
    {
        return $this->hasMany(DeThi::class, 'id_cau_hoi');
    }

    public function dapAns()
    {
        return $this->hasMany(DapAn::class, 'id_cau_hoi');
    }
}
