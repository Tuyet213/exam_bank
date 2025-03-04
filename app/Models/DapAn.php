<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class DapAn extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_cau_hoi',
        'dap_an',
        'trang_thai'
    ];

    public function cauHoi()
    {
        return $this->belongsTo(CauHoi::class, 'id_cau_hoi');
    }
    
    
}
