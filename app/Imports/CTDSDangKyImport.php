<?php

namespace App\Imports;

use App\Models\CTDSDangKy;
use App\Models\DSGVBienSoan;
use App\Models\HocPhan;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CTDSDangKyImport implements ToCollection, WithHeadingRow
{
    protected $id_ds_dang_ky;
    protected $id_bo_mon;

    public function __construct($id_ds_dang_ky, $id_bo_mon)
    {
        $this->id_ds_dang_ky = $id_ds_dang_ky;
        $this->id_bo_mon = $id_bo_mon;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                DB::beginTransaction();
                
                // Xử lý dữ liệu từ file Excel
                $ma_hoc_phan = $row['ma_hoc_phan'] ?? null;
                $ten_vien_chuc = $row['ten_vien_chuc'] ?? null;
                $hinh_thuc_thi = $row['hinh_thuc_thi'] ?? 'Trắc nghiệm';
                $so_luong = $row['so_luong'] ?? 1;
                
                // Kiểm tra dữ liệu hợp lệ
                if (!$ma_hoc_phan || !$ten_vien_chuc) {
                    Log::warning('Import dữ liệu bị bỏ qua - thiếu thông tin', ['row' => $row]);
                    continue;
                }
                
                // Tìm học phần theo mã
                $hoc_phan = HocPhan::where('id', $ma_hoc_phan)
                    ->where('id_bo_mon', $this->id_bo_mon)
                    ->first();
                
                if (!$hoc_phan) {
                    Log::warning('Import dữ liệu bị bỏ qua - không tìm thấy học phần', ['ma_hoc_phan' => $ma_hoc_phan]);
                    continue;
                }
                
                // Tìm viên chức theo tên
                $vien_chuc = User::where('name', 'like', "%{$ten_vien_chuc}%")
                    ->where('id_bo_mon', $this->id_bo_mon)
                    ->first();
                    
                if (!$vien_chuc) {
                    Log::warning('Import dữ liệu bị bỏ qua - không tìm thấy viên chức', ['ten_vien_chuc' => $ten_vien_chuc]);
                    continue;
                }
                
                // Chuẩn hóa hình thức thi
                if (!in_array($hinh_thuc_thi, ['Trắc nghiệm', 'Tự luận', 'Trắc nghiệm và tự luận'])) {
                    $hinh_thuc_thi = 'Trắc nghiệm';
                }
                
                // Tạo chi tiết danh sách đăng ký
                $ctDSDangKy = CTDSDangKy::create([
                    'id_ds_dang_ky' => $this->id_ds_dang_ky,
                    'id_hoc_phan' => $hoc_phan->id,
                    'hinh_thuc_thi' => $hinh_thuc_thi,
                    'so_luong' => $so_luong,
                    'trang_thai' => 'Draft',
                    'able' => true,
                    'so_gio' => 0,
                ]);
                
                // Tạo bản ghi viên chức biên soạn
                DSGVBienSoan::create([
                    'id_ct_ds_dang_ky' => $ctDSDangKy->id,
                    'id_vien_chuc' => $vien_chuc->id
                ]);
                
                DB::commit();
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Lỗi khi import dữ liệu: ' . $e->getMessage(), ['row' => $row]);
            }
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
} 