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
            // Xóa foreign key constraint trước khi xóa cột
            $table->dropForeign(['id_lop_hoc_phan']);
            
            // Xóa các cột không cần thiết
            $table->dropColumn([
                'id_lop_hoc_phan',
                'hoc_ky', 
                'nam',
                'ngay_thi',
                'su_dung_tai_lieu',
                'able'
            ]);
            
            // Thêm các cột mới
            $table->text('de')->nullable()->comment('Đường link file đề thi');
            $table->text('dap_an')->nullable()->comment('Đường link file đáp án');
            
            // Cập nhật cột loai để rõ ràng hơn
            $table->string('loai')->default('Trắc nghiệm')->comment('Loại đề thi: Trắc nghiệm, Tự luận/Vấn đáp')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('de_this', function (Blueprint $table) {
            // Thêm lại các cột đã xóa
            $table->unsignedBigInteger('id_lop_hoc_phan')->nullable();
            $table->string('hoc_ky')->nullable();
            $table->string('nam')->nullable();
            $table->date('ngay_thi')->nullable();
            $table->string('su_dung_tai_lieu')->nullable();
            $table->boolean('able')->default(true);
            
            // Thêm lại foreign key constraint
            $table->foreign('id_lop_hoc_phan')->references('id')->on('lop_hoc_phans');
            
            // Xóa các cột mới
            $table->dropColumn(['de', 'dap_an']);
            
            // Khôi phục cột loai về dạng cũ
            $table->string('loai')->change();
        });
    }
};
