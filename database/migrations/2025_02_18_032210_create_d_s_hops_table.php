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
        Schema::create('d_s_hops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bien_ban_hop')->constrained('bien_ban_hops');
            $table->foreignId('id_nhiem_vu')->constrained('nhiem_vus');
            $table->string('id_vien_chuc', 6);
            $table->foreign('id_vien_chuc')->references('id')->on('vien_chucs');
            $table->integer('so_gio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_s_hops');
    }
};
