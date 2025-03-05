<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChucVu extends Model
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

    public function vienChucs()
    {
        return $this->hasMany(  User::class, 'id_chuc_vu');
    }
}
