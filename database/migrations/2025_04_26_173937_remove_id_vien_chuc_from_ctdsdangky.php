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
            $table->dropForeign(['id_vien_chuc']);
            $table->dropColumn('id_vien_chuc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('c_t_d_s_dang_kies', function (Blueprint $table) {
            $table->string('id_vien_chuc')->nullable();
            $table->foreign('id_vien_chuc')->references('id')->on('users');
        });
    }
};
