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
        Schema::create('chuong_chuan_dau_ras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_chuong')->constrained('chuongs');
            $table->foreignId('id_chuan_dau_ra')->constrained('chuan_dau_ras');
            $table->boolean('able')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chuong_chuan_dau_ras');
    }
};
