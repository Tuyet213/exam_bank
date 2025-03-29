<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class BoMonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bo_mons')->insert([
            [
                'id' => 'admin',
                'id_khoa' => 'admin',
                'ten' => 'Admin',
            ],
            [
                'id' => 'dbcl',
                'id_khoa' => 'DBCL',
                'ten' => 'Đảm bảo chất lượng',
            ],
            [
                'id' => 'CNPM',
                'id_khoa' => 'CNTT',
                'ten' => 'Công nghệ phần mềm',
            ],
        ]);
    }
}
