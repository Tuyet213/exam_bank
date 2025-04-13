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
        Schema::table('c_t_d_s_dang_kies', function (Blueprint $table) {
            $table->boolean('loai_ngan_hang')->default(false)->after('id_vien_chuc');
            $table->integer('so_luong')->default(0)->after('loai_ngan_hang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('c_t_d_s_dang_kies', function (Blueprint $table) {
            $table->dropColumn('loai_ngan_hang');
            $table->dropColumn('so_luong');
        });
    }
};
