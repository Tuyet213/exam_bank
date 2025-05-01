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
        Schema::table('cau_hois', function (Blueprint $table) {
            $table->string('muc_do')->nullable()->after('cau_hoi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cau_hois', function (Blueprint $table) {
            $table->dropColumn('muc_do');
        });
    }
};
