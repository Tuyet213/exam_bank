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
            $table->string('hinh_thuc_thi')->nullable()->after('loai_ngan_hang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('c_t_d_s_dang_kies', function (Blueprint $table) {
            $table->dropColumn('hinh_thuc_thi');
        });
    }
};
