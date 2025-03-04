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
        Schema::create('d_s_dang_kies', function (Blueprint $table) {
            $table->id();
            $table->string('id_bo_mon', 6);
            $table->foreign('id_bo_mon')->references('id')->on('bo_mons');
            $table->dateTime('thoi_gian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_s_dang_kies');
    }
};
