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
        Schema::create('bien_ban_hops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_ct_ds_dang_ky')->constrained('c_t_d_s_dang_kies');
            $table->dateTime('thoi_gian');
            $table->string('noi_dung');
            $table->string('dia_diem');
            $table->string('loai');
            $table->boolean('able')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bien_ban_hops');
    }
};
