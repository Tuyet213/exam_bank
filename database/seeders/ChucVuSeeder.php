<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ChucVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chuc_vus')->insert([
            [
                'id' => 'admin',
                'ten' => 'Admin',
            ],
            
            [
                'id' => 'gv',
                'ten' => 'Giảng viên',
            ],
            [
                'id' => 'tk',
                'ten' => 'Trưởng khoa',
            ],
            [
                'id' => 'tbm',
                'ten' => 'Trưởng bộ môn',
            ],
            [
                'id' => 'nvdbcl',
                'ten' => 'Nhân viên phòng đảm bảo chất lượng',
            ],
        ]);
        DB::table('nhiem_vus')->insert([
            [
                
                'ten' => 'Chủ tịch',
            ],
            [
               
                'ten' => 'Thư ký',
            ],
            [
                
                'ten' => 'Cán bộ phản biện',
            ],
        ]);
    }
}
