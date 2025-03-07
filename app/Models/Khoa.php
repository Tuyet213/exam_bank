<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Khoa extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'ten',
        'able'
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function boMons()
    {
        return $this->hasMany(BoMon::class, 'id_khoa');
    }
    public function lopHocPhans()
    {
        return $this->hasMany(LopHocPhan::class, 'id_khoa');
    }
}
