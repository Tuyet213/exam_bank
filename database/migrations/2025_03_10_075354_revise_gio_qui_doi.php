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
        Schema::table('gio_quy_dois', function (Blueprint $table) {
            $table->dropColumn('tin_chi');
            $table->dropColumn('cau');
            $table->string('loai_de_thi')->nullable();
            $table->string('loai_hanh_dong')->nullable();
            $table->integer('so_luong')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gio_quy_dois', function (Blueprint $table) {
            $table->integer('tin_chi')->nullable();
            $table->integer('cau')->nullable();
            $table->dropColumn('loai_de_thi');
            $table->dropColumn('loai_hanh_dong');
            $table->dropColumn('so_luong');
        });
    }
};
