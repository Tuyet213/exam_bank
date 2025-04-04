<?php

namespace App\Imports;

use App\Models\CTDSDangKy;
use App\Models\HocPhan;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;


class CTDSDangKyImport implements ToModel, WithHeadingRow, WithValidation
{
    private $id_ds_dang_ky;
    private $id_bo_mon;

    public function __construct($id_ds_dang_ky, $id_bo_mon)
    {
        $this->id_ds_dang_ky = $id_ds_dang_ky;
        $this->id_bo_mon = $id_bo_mon;
    }

    public function model(array $row)
    {
        // Kiểm tra học phần có thuộc bộ môn không
        $hocPhan = HocPhan::find($row['id_hoc_phan']);
        if (!$hocPhan || $hocPhan->id_bo_mon != $this->id_bo_mon) {
            throw new \Exception("Học phần {$row['id_hoc_phan']} không thuộc bộ môn này");
        }

        // Kiểm tra viên chức có thuộc bộ môn không
        $vienChuc = User::find($row['id_vien_chuc']);
        if (!$vienChuc || $vienChuc->id_bo_mon != $this->id_bo_mon) {
            throw new \Exception("Viên chức {$row['id_vien_chuc']} không thuộc bộ môn này");
        }

        // Kiểm tra xem đã tồn tại bản ghi chưa
        $existingRecord = CTDSDangKy::where('id_ds_dang_ky', $this->id_ds_dang_ky)
            ->where('id_hoc_phan', $row['id_hoc_phan'])
            ->where('id_vien_chuc', $row['id_vien_chuc'])
            ->first();

        if ($existingRecord) {
            // Nếu đã tồn tại thì cập nhật số giờ
            $existingRecord->update([
                'so_gio' => $row['so_gio']
            ]);
            return null; // Trả về null để không tạo bản ghi mới
        }

        // Nếu chưa tồn tại thì tạo mới
        return new CTDSDangKy([
            'id_ds_dang_ky' => $this->id_ds_dang_ky,
            'id_hoc_phan' => $row['id_hoc_phan'],
            'id_vien_chuc' => $row['id_vien_chuc'],
            'so_gio' => $row['so_gio'],
            'trang_thai' => 'Draft'
        ]);
    }

    public function rules(): array
    {
        return [
            'id_hoc_phan' => 'required|exists:hoc_phans,id',
            'id_vien_chuc' => 'required|exists:users,id',
            'so_gio' => 'required|numeric|min:0'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'id_hoc_phan.required' => 'Mã học phần không được để trống',
            'id_hoc_phan.exists' => 'Mã học phần không tồn tại',
            'id_vien_chuc.required' => 'Mã viên chức không được để trống',
            'id_vien_chuc.exists' => 'Mã viên chức không tồn tại',
            'so_gio.required' => 'Số giờ không được để trống',
            'so_gio.numeric' => 'Số giờ phải là số',
            'so_gio.min' => 'Số giờ phải lớn hơn 0'
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
} 