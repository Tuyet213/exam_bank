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
        Schema::create('hoc_phans', function (Blueprint $table) {
            $table->string('id', 6)->primary();
            $table->string('ten', 255);
            $table->tinyInteger('so_tin_chi', false, true);
            $table->double('hoc_phi');
            $table->boolean('able')->default(true);
            $table->string('id_bo_mon', 6);
            $table->string('id_bac_dao_tao', 6);
            $table->foreign('id_bo_mon')->references('id')->on('bo_mons');
            $table->foreign('id_bac_dao_tao')->references('id')->on('bac_dao_taos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoc_phans');
    }
};
