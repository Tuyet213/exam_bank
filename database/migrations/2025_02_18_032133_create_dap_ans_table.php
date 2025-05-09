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
        Schema::create('dap_ans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cau_hoi')->constrained('cau_hois');
            $table->string('dap_an');
            $table->decimal('diem', 4, 2)->default(0);
            $table->boolean('trang_thai');
            $table->boolean('able')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_ans');
    }
};
