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
        Schema::create('c_t_de_this', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_de_thi')->constrained('de_this');
            $table->foreignId('id_cau_hoi')->constrained('cau_hois');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_t_de_this');
    }
};
