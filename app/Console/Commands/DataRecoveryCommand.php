<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DSDangKy;
use App\Models\CTDSDangKy;
use App\Models\DSGVBienSoan;
use App\Models\BienBanHop;
use App\Models\DSHop;
use App\Models\User;
use App\Models\HocPhan;
use App\Models\NhiemVu;
use App\Models\BoMon;
use Illuminate\Support\Facades\DB;

class DataRecoveryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:recover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Phục hồi dữ liệu sau khi bị mất';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu quá trình phục hồi dữ liệu...');
        
        try {
            DB::beginTransaction();
            
            // Kiểm tra bộ môn
            $boMon = BoMon::first();
            if (!$boMon) {
                $this->error('Không tìm thấy bộ môn. Vui lòng chạy seeder RecoverySeeder trước.');
                return;
            }
            
            // Kiểm tra học phần
            $hocPhan = HocPhan::first();
            if (!$hocPhan) {
                $this->error('Không tìm thấy học phần. Vui lòng chạy seeder RecoverySeeder trước.');
                return;
            }
            
            // Kiểm tra người dùng
            $users = User::where('id_bo_mon', $boMon->id)->get();
            if ($users->isEmpty()) {
                $this->error('Không tìm thấy người dùng thuộc bộ môn. Vui lòng chạy seeder UserSeeder trước.');
                return;
            }
            
            // 1. Tạo một danh sách đăng ký mẫu
            $dsDangKy = DSDangKy::create([
                'hoc_ki' => '1',
                'nam_hoc' => '2024-2025',
                'id_bo_mon' => $boMon->id,
                'able' => 1
            ]);
            
            $this->info("Đã tạo danh sách đăng ký ID: {$dsDangKy->id}");
            
            // 2. Tạo chi tiết đăng ký
            $ctDSDangKy = CTDSDangKy::create([
                'id_ds_dang_ky' => $dsDangKy->id,
                'id_hoc_phan' => $hocPhan->id,
                'loai_ngan_hang' => 1, // Ngân hàng câu hỏi
                'so_luong' => 100,
                'hinh_thuc_thi' => 'Trắc nghiệm',
                'trang_thai' => 'Draft',
                'able' => 1
            ]);
            
            $this->info("Đã tạo chi tiết đăng ký ID: {$ctDSDangKy->id}");
            
            // 3. Thêm giảng viên biên soạn
            foreach ($users->take(2) as $user) {
                $dsGVBienSoan = DSGVBienSoan::create([
                    'id_ct_ds_dang_ky' => $ctDSDangKy->id,
                    'id_vien_chuc' => $user->id,
                    'so_gio' => 10
                ]);
                
                $this->info("Đã thêm giảng viên biên soạn: {$user->name}");
            }
            
            // 4. Tạo biên bản họp
            $bienBan = BienBanHop::create([
                'id_ct_ds_dang_ky' => $ctDSDangKy->id,
                'thoi_gian' => now(),
                'dia_diem' => 'Phòng họp khoa',
                'cap' => 'Bộ môn',
                'able' => 1
            ]);
            
            $this->info("Đã tạo biên bản họp ID: {$bienBan->id}");
            
            // 5. Thêm danh sách người tham gia họp
            $nhiemVus = NhiemVu::all();
            
            foreach ($nhiemVus as $index => $nhiemVu) {
                if (isset($users[$index])) {
                    $dsHop = DSHop::create([
                        'id_bien_ban_hop' => $bienBan->id,
                        'id_vien_chuc' => $users[$index]->id,
                        'id_nhiem_vu' => $nhiemVu->id,
                        'so_gio' => 2,
                        'able' => 1
                    ]);
                    
                    $this->info("Đã thêm người tham gia họp: {$users[$index]->name} - {$nhiemVu->ten}");
                }
            }
            
            DB::commit();
            $this->info('Quá trình phục hồi dữ liệu đã hoàn tất thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Đã xảy ra lỗi trong quá trình phục hồi: ' . $e->getMessage());
            $this->error('Chi tiết: ' . $e->getTraceAsString());
        }
    }
} 