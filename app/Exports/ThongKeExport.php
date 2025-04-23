<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ThongKeExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $thongke_data;

    public function __construct($thongke_data)
    {
        $this->thongke_data = $thongke_data;
    }

    /**
     * Tiêu đề của sheet
     */
    public function title(): string
    {
        return 'THỐNG KÊ BIÊN SOẠN NGÂN HÀNG ĐỀ THI/CÂU HỎI';
    }

    /**
     * Dữ liệu chuyển đổi sang dạng collection để xuất Excel
     */
    public function collection()
    {
        $collection = new Collection();
        
        // Biến đếm STT
        $stt = 1;
        
        // Duyệt qua cấu trúc dữ liệu đã được tổ chức
        foreach ($this->thongke_data as $nam_hoc => $nam_data) {
            foreach ($nam_data['hoc_ki'] as $hoc_ki => $hoc_ki_data) {
                foreach ($hoc_ki_data['khoa'] as $khoa) {
                    foreach ($khoa['bomon'] as $bomon) {
                        // Thêm hàng nhóm thông tin chung
                        $collection->push([
                            'stt' => '',
                            'nam_hoc' => $nam_hoc,
                            'hoc_ki' => 'Học kỳ ' . $hoc_ki,
                            'khoa' => $khoa['ten'],
                            'bomon' => $bomon['ten'],
                            'hoc_phan' => '',
                            'giang_vien' => '',
                            'nguoi_phan_bien' => '',
                            'so_gio' => '',
                            'hinh_thuc_thi' => '',
                            'loai_ngan_hang' => '',
                            'so_luong' => ''
                        ]);
                        
                        foreach ($bomon['chitiet'] as $chitiet) {
                            $collection->push([
                                'stt' => $stt++,
                                'nam_hoc' => '',  // Để trống vì đã có ở hàng nhóm
                                'hoc_ki' => '',   // Để trống vì đã có ở hàng nhóm
                                'khoa' => '',     // Để trống vì đã có ở hàng nhóm
                                'bomon' => '',    // Để trống vì đã có ở hàng nhóm
                                'hoc_phan' => $chitiet['hoc_phan'],
                                'giang_vien' => $chitiet['giang_vien'],
                                'nguoi_phan_bien' => $chitiet['nguoi_phan_bien'],
                                'so_gio' => $chitiet['so_gio'],
                                'hinh_thuc_thi' => $chitiet['hinh_thuc_thi'],
                                'loai_ngan_hang' => $chitiet['loai_ngan_hang'],
                                'so_luong' => $chitiet['so_luong']
                            ]);
                        }
                    }
                }
            }
        }
        
        return $collection;
    }

    /**
     * Tiêu đề các cột
     */
    public function headings(): array
    {
        return [
            'STT',
            'Năm học',
            'Học kỳ',
            'Khoa',
            'Bộ môn',
            'Học phần',
            'Giảng viên biên soạn',
            'Người phản biện cấp bộ môn',
            'Số giờ',
            'Hình thức thi',
            'Loại ngân hàng',
            'Số lượng'
        ];
    }

    /**
     * Định dạng styles cho bảng
     */
    public function styles(Worksheet $sheet)
    {
        // Định dạng tiêu đề
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0070C0'], // Màu xanh dương
            ],
        ]);
        
        // Định dạng các hàng dữ liệu
        $dataRows = $sheet->getHighestRow();
        
        // Định dạng các hàng thông tin chung
        $rowNumber = 2; // Bắt đầu từ hàng 2 (sau tiêu đề)
        while ($rowNumber <= $dataRows) {
            // Nếu cột năm học (B) có giá trị nhưng cột Học phần (F) không có giá trị
            // thì đây là hàng thông tin chung
            if ($sheet->getCell('B'.$rowNumber)->getValue() != '' && $sheet->getCell('F'.$rowNumber)->getValue() == '') {
                $sheet->getStyle('A'.$rowNumber.':L'.$rowNumber)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFDBE5F1'], // Màu xanh nhạt
                    ],
                ]);
            }
            $rowNumber++;
        }
        
        // Đường viền cho tất cả các ô dữ liệu
        $sheet->getStyle('A1:L'.$dataRows)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
        
        return [];
    }
} 