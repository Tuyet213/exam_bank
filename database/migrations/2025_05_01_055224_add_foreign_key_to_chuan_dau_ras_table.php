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
        Schema::table('chuan_dau_ras', function (Blueprint $table) {
            $table->foreign('id_hoc_phan')
                  ->references('id')
                  ->on('hoc_phans')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chuan_dau_ras', function (Blueprint $table) {
            $table->dropForeign(['id_hoc_phan']);
        });
    }
};
