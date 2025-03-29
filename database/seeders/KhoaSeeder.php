<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KhoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('khoas')->insert([
            [
                'id' => 'admin',
                'ten' => 'Admin',
            ],
            [
                'id' => 'DBCL',
                'ten' => 'Đảm bảo chất lượng',
            ],
            [
                'id' => 'CNTT',
                'ten' => 'Công nghệ thông tin',
            ],
        ]);
    }
}

