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
        Schema::table('ma_trans', function (Blueprint $table) {
            $table->enum('loai_ky', ['giua_ky', 'cuoi_ky'])->default('cuoi_ky')->after('able')->comment('Loại kỳ: giữa kỳ hoặc cuối kỳ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ma_trans', function (Blueprint $table) {
            $table->dropColumn('loai_ky');
        });
    }
};
