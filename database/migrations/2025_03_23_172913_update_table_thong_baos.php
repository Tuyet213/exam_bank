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
        Schema::table('thong_baos', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('content')->nullable()->change();
            $table->string('files')->nullable()->change();
            $table->boolean('able')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thong_baos', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->string('content')->nullable(false)->change();
            $table->string('files')->nullable(false)->change();
            $table->boolean('able')->default(true)->change();
        });
    }
};
