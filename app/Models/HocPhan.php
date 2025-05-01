<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class HocPhan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'ten',
        'so_tin_chi',
        'id_bo_mon',
        'id_bac_dao_tao',
        'able'
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function boMon()
    {
        return $this->belongsTo(BoMon::class, 'id_bo_mon');
    }

    public function bacDaoTao()
    {
        return $this->belongsTo(BacDaoTao::class, 'id_bac_dao_tao');
    }

    public function deThis()
    {
        return $this->hasMany(DeThi::class, 'id_hoc_phan');
    }

    public function chuongs()
    {
        return $this->hasMany(Chuong::class, 'id_hoc_phan');
    }
    

    public function maTrans()
    {
        return $this->hasMany(MaTran::class, 'id_hoc_phan');
    }

    public function cauHois()
    {
        return $this->hasMany(CauHoi::class, 'id_hoc_phan');
    }

    public function ctDSDangKies()
    {
        return $this->hasMany(CTDSDangKy::class, 'id_hoc_phan');
    }

    public function lopHocPhans()
    {
        return $this->hasMany(LopHocPhan::class, 'id_hoc_phan');
    }

    public function chuanDauRas()
    {
        return $this->hasMany(ChuanDauRa::class, 'id_hoc_phan');
    }

    public function chuongChuanDauRas()
    {
        return $this->hasMany(ChuongChuanDauRa::class, 'id_hoc_phan');
    }

}
