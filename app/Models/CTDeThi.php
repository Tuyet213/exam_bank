<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CTDeThi extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_de_thi',
        'id_cau_hoi',
    ];

    public function deThi()
    {
        return $this->belongsTo(DeThi::class, 'id_de_thi');
    }

    public function cauHoi()
    {
        return $this->belongsTo(CauHoi::class, 'id_cau_hoi');
    }

    

}
