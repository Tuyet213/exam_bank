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
        Schema::table('d_s_hops', function (Blueprint $table) {
            $table->decimal('so_gio', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('d_s_hops', function (Blueprint $table) {
            $table->decimal('so_gio', 8, 2)->nullable(false)->change();
        });
    }
};
