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
        if (!Schema::hasColumn('d_s_g_v_bien_soans', 'so_gio')) {
            Schema::table('d_s_g_v_bien_soans', function (Blueprint $table) {
                $table->float('so_gio')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('d_s_g_v_bien_soans', 'so_gio')) {
            Schema::table('d_s_g_v_bien_soans', function (Blueprint $table) {
                $table->dropColumn('so_gio');
            });
        }
    }
};
