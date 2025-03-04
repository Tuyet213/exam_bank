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
        Schema::create('c_t_d_s_dang_kies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_ds_dang_ky')->constrained('d_s_dang_kies');
            $table->string('id_hoc_phan', 6);
            $table->foreign('id_hoc_phan')->references('id')->on('hoc_phans');
            $table->string('id_vien_chuc', 6);
            $table->foreign('id_vien_chuc')->references('id')->on('vien_chucs');
            $table->integer('so_gio');
            $table->string('trang_thai', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_t_d_s_dang_kies');
    }
};
