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
        Schema::create('chuongs', function (Blueprint $table) {
            $table->id();
            $table->string('ten', 255);
            $table->string('id_hoc_phan', 6);
            $table->boolean('able')->default(true);
            $table->foreign('id_hoc_phan')->references('id')->on('hoc_phans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chuongs');
    }
};
