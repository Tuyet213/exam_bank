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
            'create-questions',
            'extract-exam-matrix',
            'approve-exam-registration'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo($permissions);

        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $teacher->givePermissionTo(['create-questions', 'extract-exam-matrix']);

        $qualitySurvey = Role::firstOrCreate(['name' => 'quality-survey']);
        $qualitySurvey->givePermissionTo(['approve-exam-registration']);

        $TBM = Role::firstOrCreate(['name' => 'TBM']);
        $TK = Role::firstOrCreate(['name' => 'TK']);
            
    }
}
