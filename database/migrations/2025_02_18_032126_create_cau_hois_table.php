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
        Schema::create('cau_hois', function (Blueprint $table) {
            $table->id();
            $table->text('cau_hoi');
            $table->foreignId('id_ct_ds_dang_ky')->constrained('c_t_d_s_dang_kies');
            $table->foreignId('id_chuan_dau_ra')->constrained('chuan_dau_ras');
            $table->foreignId('id_chuong')->constrained('chuongs');
            $table->string('phan_loai');
            $table->double('diem');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cau_hois');
    }
};
