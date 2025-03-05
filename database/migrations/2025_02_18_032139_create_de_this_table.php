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
        Schema::create('de_this', function (Blueprint $table) {
            $table->id();
            $table->string('id_hoc_phan', 6);
            $table->foreign('id_hoc_phan')->references('id')->on('hoc_phans');
            $table->foreignId('id_lop_hoc_phan')->constrained('lop_hoc_phans');
            $table->tinyInteger('hoc_ky');
            $table->smallInteger('nam');
            $table->date('ngay_thi');
            $table->string('loai');
            $table->boolean('su_dung_tai_lieu');
            $table->boolean('able')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('de_this');
    }
};
