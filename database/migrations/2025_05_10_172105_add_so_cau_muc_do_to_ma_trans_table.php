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
        Schema::table('ma_trans', function (Blueprint $table) {
            $table->integer('so_cau_de')->default(0);
            $table->integer('so_cau_tb')->default(0);
            $table->integer('so_cau_kho')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ma_trans', function (Blueprint $table) {
            $table->dropColumn(['so_cau_de', 'so_cau_tb', 'so_cau_kho']);
        });
    }
};
