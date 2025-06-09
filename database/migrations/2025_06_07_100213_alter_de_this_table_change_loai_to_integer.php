<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm cột mới loai_new (integer)
        Schema::table('de_this', function (Blueprint $table) {
            $table->tinyInteger('loai_new')->default(1)->after('loai')->comment('0: Trắc nghiệm, 1: Tự luận/Vấn đáp');
        });

        // Convert dữ liệu từ string sang integer
        DB::statement("
            UPDATE de_this 
            SET loai_new = CASE 
                WHEN LOWER(loai) LIKE '%trắc%' OR LOWER(loai) LIKE '%trac%' THEN 0
                ELSE 1
            END
        ");

        // Xóa cột loai cũ và rename loai_new thành loai
        Schema::table('de_this', function (Blueprint $table) {
            $table->dropColumn('loai');
        });

        Schema::table('de_this', function (Blueprint $table) {
            $table->renameColumn('loai_new', 'loai');
        });

        // Thêm index cho cột loai
        Schema::table('de_this', function (Blueprint $table) {
            $table->index('loai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Thêm cột loai_old (string)
        Schema::table('de_this', function (Blueprint $table) {
            $table->dropIndex(['loai']);
            $table->string('loai_old', 50)->after('loai');
        });

        // Convert dữ liệu từ integer về string
        DB::statement("
            UPDATE de_this 
            SET loai_old = CASE 
                WHEN loai = 0 THEN 'Trắc nghiệm'
                WHEN loai = 1 THEN 'Tự luận/Vấn đáp'
                ELSE 'Tự luận/Vấn đáp'
            END
        ");

        // Xóa cột loai cũ và rename loai_old thành loai
        Schema::table('de_this', function (Blueprint $table) {
            $table->dropColumn('loai');
        });

        Schema::table('de_this', function (Blueprint $table) {
            $table->renameColumn('loai_old', 'loai');
        });
    }
};
