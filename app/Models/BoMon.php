<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoMon extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'ten',
        'id_khoa',
        'able'
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'id_khoa');
    }

    public function hocPhans()
    {
        return $this->hasMany(HocPhan::class, 'id_bo_mon');
    }
    public function vienChucs()
    {
        return $this->hasMany(User::class, 'id_bo_mon');
    }
}
