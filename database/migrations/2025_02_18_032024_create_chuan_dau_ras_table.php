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
        Schema::create('chuan_dau_ras', function (Blueprint $table) {
            $table->id();         
            $table->string('ten', 255);
            $table->text('noi_dung');
            $table->string('id_hoc_phan', 6);
            $table->foreign('id_hoc_phan')->references('id')->on('hoc_phans')->onDelete('cascade');
            $table->boolean('able')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chuan_dau_ras');
    }
};
