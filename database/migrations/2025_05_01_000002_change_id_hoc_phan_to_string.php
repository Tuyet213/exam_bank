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
        Schema::table('hoc_phans', function (Blueprint $table) {
            $table->string('id', 6)->change();
        });

        Schema::table('chuan_dau_ras', function (Blueprint $table) {
            if (Schema::hasColumn('chuan_dau_ras', 'id_hoc_phan')) {
                $table->string('id_hoc_phan', 6)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoc_phans', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->change();
        });

        Schema::table('chuan_dau_ras', function (Blueprint $table) {
            if (Schema::hasColumn('chuan_dau_ras', 'id_hoc_phan')) {
                $table->unsignedBigInteger('id_hoc_phan')->change();
            }
        });
    }
}; 