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
        if (Schema::hasColumn('c_t_d_s_dang_kies', 'so_gio')) {
            Schema::table('c_t_d_s_dang_kies', function (Blueprint $table) {
                $table->dropColumn('so_gio');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('c_t_d_s_dang_kies', 'so_gio')) {
            Schema::table('c_t_d_s_dang_kies', function (Blueprint $table) {
                $table->float('so_gio')->default(0);
            });
        }
    }
};
