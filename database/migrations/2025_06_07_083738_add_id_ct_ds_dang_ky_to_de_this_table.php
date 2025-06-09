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
        Schema::table('de_this', function (Blueprint $table) {
            // Thêm column id_ct_ds_dang_ky sau column id_hoc_phan
            $table->foreignId('id_ct_ds_dang_ky')->after('id_hoc_phan')->constrained('c_t_d_s_dang_kies', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('de_this', function (Blueprint $table) {
            // Xóa foreign key constraint trước
            $table->dropForeign(['id_ct_ds_dang_ky']);
            // Sau đó xóa column
            $table->dropColumn('id_ct_ds_dang_ky');
        });
    }
};
