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
        Schema::table('bien_ban_hops', function (Blueprint $table) {
            $table->string('cap')->nullable();
            $table->dropColumn('loai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bien_ban_hops', function (Blueprint $table) {
            $table->dropColumn('cap');
            $table->string('loai')->nullable();
        });
    }
};
