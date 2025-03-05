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
        Schema::create('bo_mons', function (Blueprint $table) {
            $table->string('id', 6)->primary();
            $table->string('ten', 255);
            $table->boolean('able')->default(true);
            $table->string('id_khoa', 6);
            $table->foreign('id_khoa')->references('id')->on('khoas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bo_mons');
    }
};
