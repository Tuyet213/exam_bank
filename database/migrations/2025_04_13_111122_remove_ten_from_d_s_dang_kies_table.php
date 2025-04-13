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
        Schema::table('d_s_dang_kies', function (Blueprint $table) {
            $table->dropColumn('ten');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('d_s_dang_kies', function (Blueprint $table) {
            $table->string('ten')->after('id');
        });
    }
};
