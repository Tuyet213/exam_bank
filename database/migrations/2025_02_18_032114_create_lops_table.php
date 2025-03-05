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
        Schema::create('lop_hoc_phans', function (Blueprint $table) {
            $table->id();
            $table->string('ten', 255);
            $table->string('ky_hoc', 255);
            $table->string('nam_hoc', 255);
            $table->integer('so_luong_sinh_vien');
            $table->string('id_khoa', 6);
            $table->foreign('id_khoa')->references('id')->on('khoas');
            $table->string('id_hoc_phan', 6);
            $table->foreign('id_hoc_phan')->references('id')->on('hoc_phans');
            $table->string('id_vien_chuc', 6);
            $table->foreign('id_vien_chuc')->references('id')->on('users');
            $table->boolean('able')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lops');
    }
};
