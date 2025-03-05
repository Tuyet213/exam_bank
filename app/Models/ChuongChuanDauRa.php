<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ChuongChuanDauRa extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'id_chuong',
        'id_chuan_dau_ra',
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
    
    
}
