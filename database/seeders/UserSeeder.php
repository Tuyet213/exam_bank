<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        DB::table('users')->insert([
            [
                'id' => 'admin',
                'name' => 'Admin',
                'email' => 'tuyet.htn.63cntt@ntu.edu.vn',
                'password' => Hash::make('admin'),
                'sdt' => '0905123456',
                'dia_chi' => 'Hà Nội',
                'ngay_sinh' => '2000-01-01',
                'gioi_tinh' => '0',
                'id_chuc_vu' => 'admin',
                'id_bo_mon' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'GV001',
                'name' => 'Nguyễn Văn A',
                'email' => 'huynhtuyet0201032019@gmail.com',
                'password' => Hash::make('admin'),
                'sdt' => '0905123456',
                'dia_chi' => 'Hà Nội',
                'ngay_sinh' => '2000-01-01',
                'gioi_tinh' => '0',
                'id_chuc_vu' => 'tk',
                'id_bo_mon' => 'CNPM',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'GV002',
                'name' => 'Nguyễn Văn B',
                'email' => 'nguyenvanb@gmail.com',
                'password' => Hash::make('admin'),
                'sdt' => '0905123456',
                'dia_chi' => 'Hà Nội',
                'ngay_sinh' => '2000-01-01',
                'gioi_tinh' => '0',
                'id_chuc_vu' => 'tbm',
                'id_bo_mon' => 'CNPM',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'NV001',
                'name' => 'Nguyễn Văn C',
                'email' => 'nguyenvanc@gmail.com',
                'password' => Hash::make('admin'),
                'sdt' => '0905123456',
                'dia_chi' => 'Hà Nội',
                'ngay_sinh' => '2000-01-01',
                'gioi_tinh' => '0',
                'id_chuc_vu' => 'nvdbcl',
                'id_bo_mon' => 'DBCL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        

        // Gán role cho user
        $admin = Role::findByName('Admin');
        $teacher = Role::findByName('Giảng viên');
        $qualitySurvey = Role::findByName('Nhân viên P.ĐBCL');
        $TBM = Role::findByName('Trưởng Bộ Môn');
        $TK = Role::findByName('Trưởng Khoa');

        DB::table('model_has_roles')->insert([
            [
                'role_id' => $admin->id,
                'model_type' => 'App\Models\User',
                'model_id' => 'admin',
            ],
            [
                'role_id' => $TK->id,
                'model_type' => 'App\Models\User',
                'model_id' => 'GV001'
            ],
            [
                'role_id' => $TBM->id,
                'model_type' => 'App\Models\User',
                'model_id' => 'GV002'
            ],
            [
                'role_id' => $qualitySurvey->id,
                'model_type' => 'App\Models\User',
                'model_id' => 'NV001'
            ],
        ]);
    }
}
