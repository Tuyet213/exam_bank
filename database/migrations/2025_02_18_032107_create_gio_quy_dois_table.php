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
        Schema::create('gio_quy_dois', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('gio');
            $table->tinyInteger('tin_chi');
            $table->tinyInteger('cau'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gio_quy_dois');
    }
};
