<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'Tạo ngân hàng câu hỏi/đề thi',
            'Xuất đề thi',
            'Duyệt danh sách đăng ký'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo($permissions);

        $teacher = Role::firstOrCreate(['name' => 'Giảng viên']);
        $teacher->givePermissionTo(['Tạo ngân hàng câu hỏi/đề thi', 'Xuất đề thi']);

        $qualitySurvey = Role::firstOrCreate(['name' => 'Nhân viên P.ĐBCL']);
        $qualitySurvey->givePermissionTo(['Duyệt danh sách đăng ký']);

        $TBM = Role::firstOrCreate(['name' => 'Trưởng Bộ Môn']);
        $TK = Role::firstOrCreate(['name' => 'Trưởng Khoa']);
            
    }
}
