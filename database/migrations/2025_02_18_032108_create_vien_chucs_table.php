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
        Schema::create('vien_chucs', function (Blueprint $table) {
            $table->string('id', 6)->primary();
            $table->string('ten', 255);
            $table->string('sdt', 10);
            $table->string('email', 255);
            $table->string('dia_chi', 255);
            $table->date('ngay_sinh');
            $table->boolean('gioi_tinh');
            $table->string('id_bo_mon', 6);
            $table->foreign('id_bo_mon')->references('id')->on('bo_mons');
            $table->string('id_chuc_vu', 6);
            $table->foreign('id_chuc_vu')->references('id')->on('chuc_vus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vien_chucs');
    }
};
