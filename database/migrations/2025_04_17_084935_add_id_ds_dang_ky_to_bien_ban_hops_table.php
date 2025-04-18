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
            Schema::table('bien_ban_hops', function (Blueprint $table) {
                // Thêm cột id_ds_dang_ky nullable
                $table->unsignedBigInteger('id_ds_dang_ky')->nullable();
                
                // Thêm foreign key constraint với ON DELETE SET NULL
                $table->foreign('id_ds_dang_ky')
                    ->references('id')
                    ->on('d_s_dang_kies')
                    ->onDelete('set null');
    
                // Cho phép id_ct_ds_dang_ky nullable
                $table->unsignedBigInteger('id_ct_ds_dang_ky')->nullable()->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bien_ban_hops', function (Blueprint $table) {
            $table->dropForeign(['id_ds_dang_ky']);
            $table->dropColumn('id_ds_dang_ky');
            $table->unsignedBigInteger('id_ct_ds_dang_ky')->change();
        });
    }
};
