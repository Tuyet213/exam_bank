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
        Schema::create('d_s_g_v_bien_soans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_ct_ds_dang_ky')->constrained('c_t_d_s_dang_kies', 'id');
            $table->string('id_vien_chuc');
            $table->foreign('id_vien_chuc')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_s_g_v_bien_soans');
    }
};
