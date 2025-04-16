<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('d_s_dang_kies', function (Blueprint $table) {
            $table->string('nam_hoc')->after('hoc_ki');
        });

        Schema::table('d_s_dang_kies', function (Blueprint $table) {
            $table->dropColumn('thoi_gian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('d_s_dang_kies', function (Blueprint $table) {
            $table->date('thoi_gian')->after('hoc_ki');
        });

        Schema::table('d_s_dang_kies', function (Blueprint $table) {
            $table->dropColumn('nam_hoc');
        });
    }
};
