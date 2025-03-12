<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lop_hoc_phans', function (Blueprint $table) {
            $table->dropColumn('so_luong_sinh_vien');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lop_hoc_phans', function (Blueprint $table) {
            $table->integer('so_luong_sinh_vien');
        });
    }
};
