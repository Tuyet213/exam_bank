<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class VienChuc extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'ten',
        'sdt',
        'email',
        'dia_chi',
        'ngay_sinh',
        'gioi_tinh',
        'id_bo_mon',
        'id_chuc_vu'
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function boMon()
    {
        return $this->belongsTo(BoMon::class, 'id_bo_mon');
    }

    public function chucVu()
    {
        return $this->belongsTo(ChucVu::class, 'id_chuc_vu');
    }

    public function ctDSDangKies()
    {
        return $this->hasMany(CTDSDangKy::class, 'id_vien_chuc');
    }

    public function dsHops()
    {
        return $this->hasMany(DSHop::class, 'id_vien_chuc');
    }

}
