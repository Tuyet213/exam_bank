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
            $table->string('id_hoc_phan', 6)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chuan_dau_ras', function (Blueprint $table) {
            $table->integer('id_hoc_phan')->change();
        });
    }
}; 