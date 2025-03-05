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
        Schema::create('ma_trans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_chuan_dau_ra')->constrained('chuan_dau_ras');
            $table->foreignId('id_chuong')->constrained('chuongs');
            $table->double('diem');
            $table->boolean('able')->default(true);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ma_trans');
    }
};
