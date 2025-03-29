<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('users')->insert([
        //     [
        //         'id' => 'admin',
        //         'name' => 'Admin',
        //         'email' => 'tuyet.htn.63cntt@ntu.edu.vn',
        //         'password' => Hash::make('admin'),
        //         'sdt' => '0905123456',
        //         'dia_chi' => 'Hà Nội',
        //         'ngay_sinh' => '2000-01-01',
        //         'gioi_tinh' => '0',
        //         'id_chuc_vu' => 'admin',
        //         'id_bo_mon' => 'admin',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'id' => 'GV001',
        //         'name' => 'Nguyễn Văn A',
        //         'email' => 'huynhtuyet0201032019@gmail.com',
        //         'password' => Hash::make('admin'),
        //         'sdt' => '0905123456',
        //         'dia_chi' => 'Hà Nội',
        //         'ngay_sinh' => '2000-01-01',
        //         'gioi_tinh' => '0',
        //         'id_chuc_vu' => 'tk',
        //         'id_bo_mon' => 'CNPM',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'id' => 'GV002',
        //         'name' => 'Nguyễn Văn B',
        //         'email' => 'nguyenvanb@gmail.com',
        //         'password' => Hash::make('admin'),
        //         'sdt' => '0905123456',
        //         'dia_chi' => 'Hà Nội',
        //         'ngay_sinh' => '2000-01-01',
        //         'gioi_tinh' => '0',
        //         'id_chuc_vu' => 'tbm',
        //         'id_bo_mon' => 'CNPM',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'id' => 'NV001',
        //         'name' => 'Nguyễn Văn C',
        //         'email' => 'nguyenvanc@gmail.com',
        //         'password' => Hash::make('admin'),
        //         'sdt' => '0905123456',
        //         'dia_chi' => 'Hà Nội',
        //         'ngay_sinh' => '2000-01-01',
        //         'gioi_tinh' => '0',
        //         'id_chuc_vu' => 'nvdbcl',
        //         'id_bo_mon' => 'DBCL',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        // ]);
        DB::table('model_has_roles')->insert([
            [
                'role_id' => '1',
                'model_type' => 'App\Models\User',
                'model_id' => 'admin',
            ],
            [
                'role_id' => '5',
                'model_type' => 'App\Models\User',
                'model_id' => 'GV001'
            ],
            [
                'role_id' => '4',
                'model_type' => 'App\Models\User',
                'model_id' => 'GV002'
            ],
            [
                'role_id' => '3',
                'model_type' => 'App\Models\User',
                'model_id' => 'NV001'
            ],
        ]);
    }
}
