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
        
        // Thêm thống kê tổng hợp chung
        $collection->push([
            'stt' => '',
            'nam_hoc' => 'TỔNG HỢP CHUNG',
            'hoc_ki' => '',
            'khoa' => '',
            'bomon' => '',
            'hoc_phan' => '',
            'giang_vien' => '',
            'nguoi_phan_bien' => '',
            'tong_so_gio' => $this->thongke_data['tong_hop']['tong_so_gio'],
            'tong_so_nguoi' => $this->thongke_data['tong_hop']['tong_so_nguoi_tham_gia'],
            'tong_so_hoc_phan' => $this->thongke_data['tong_hop']['tong_so_hoc_phan'],
            'tong_so_cau_hoi' => $this->thongke_data['tong_hop']['tong_so_cau_hoi'],
            'tong_so_de_thi' => $this->thongke_data['tong_hop']['tong_so_de_thi']
        ]);
        
        // Thêm dòng trống sau tổng hợp chung
        $collection->push([
            'stt' => '', 'nam_hoc' => '', 'hoc_ki' => '', 'khoa' => '', 'bomon' => '',
            'hoc_phan' => '', 'giang_vien' => '', 'nguoi_phan_bien' => '',
            'tong_so_gio' => '', 'tong_so_nguoi' => '', 'tong_so_hoc_phan' => '',
            'tong_so_cau_hoi' => '', 'tong_so_de_thi' => ''
        ]);

        // Biến đếm STT
        $stt = 1;
        
        // Duyệt qua cấu trúc dữ liệu đã được tổ chức (bỏ qua key tong_hop)
        foreach ($this->thongke_data as $nam_hoc => $nam_data) {
            if ($nam_hoc === 'tong_hop') continue;

            // Thêm thống kê năm học
            $collection->push([
                'stt' => '',
                'nam_hoc' => "NĂM HỌC: $nam_hoc",
                'hoc_ki' => '',
                'khoa' => '',
                'bomon' => '',
                'hoc_phan' => '',
                'giang_vien' => '',
                'nguoi_phan_bien' => '',
                'tong_so_gio' => $nam_data['tong_hop']['tong_so_gio'],
                'tong_so_nguoi' => $nam_data['tong_hop']['tong_so_nguoi_tham_gia'],
                'tong_so_hoc_phan' => $nam_data['tong_hop']['tong_so_hoc_phan'],
                'tong_so_cau_hoi' => $nam_data['tong_hop']['tong_so_cau_hoi'],
                'tong_so_de_thi' => $nam_data['tong_hop']['tong_so_de_thi']
            ]);

            foreach ($nam_data['hoc_ki'] as $hoc_ki => $hoc_ki_data) {
                // Thêm thống kê học kỳ
                $collection->push([
                    'stt' => '',
                    'nam_hoc' => '',
                    'hoc_ki' => "HỌC KỲ: $hoc_ki",
                    'khoa' => '',
                    'bomon' => '',
                    'hoc_phan' => '',
                    'giang_vien' => '',
                    'nguoi_phan_bien' => '',
                    'tong_so_gio' => $hoc_ki_data['tong_hop']['tong_so_gio'],
                    'tong_so_nguoi' => $hoc_ki_data['tong_hop']['tong_so_nguoi_tham_gia'],
                    'tong_so_hoc_phan' => $hoc_ki_data['tong_hop']['tong_so_hoc_phan'],
                    'tong_so_cau_hoi' => $hoc_ki_data['tong_hop']['tong_so_cau_hoi'],
                    'tong_so_de_thi' => $hoc_ki_data['tong_hop']['tong_so_de_thi']
                ]);

                foreach ($hoc_ki_data['khoa'] as $khoa) {
                    // Thêm thống kê khoa
                    $collection->push([
                        'stt' => '',
                        'nam_hoc' => '',
                        'hoc_ki' => '',
                        'khoa' => "KHOA: {$khoa['ten']}",
                        'bomon' => '',
                        'hoc_phan' => '',
                        'giang_vien' => '',
                        'nguoi_phan_bien' => '',
                        'tong_so_gio' => $khoa['tong_hop']['tong_so_gio'],
                        'tong_so_nguoi' => $khoa['tong_hop']['tong_so_nguoi_tham_gia'],
                        'tong_so_hoc_phan' => $khoa['tong_hop']['tong_so_hoc_phan'],
                        'tong_so_cau_hoi' => $khoa['tong_hop']['tong_so_cau_hoi'],
                        'tong_so_de_thi' => $khoa['tong_hop']['tong_so_de_thi']
                    ]);

                    foreach ($khoa['bomon'] as $bomon) {
                        // Thêm thống kê bộ môn
                        $collection->push([
                            'stt' => '',
                            'nam_hoc' => '',
                            'hoc_ki' => '',
                            'khoa' => '',
                            'bomon' => "BỘ MÔN: {$bomon['ten']}",
                            'hoc_phan' => '',
                            'giang_vien' => '',
                            'nguoi_phan_bien' => '',
                            'tong_so_gio' => $bomon['tong_hop']['tong_so_gio'],
                            'tong_so_nguoi' => $bomon['tong_hop']['tong_so_nguoi_tham_gia'],
                            'tong_so_hoc_phan' => $bomon['tong_hop']['tong_so_hoc_phan'],
                            'tong_so_cau_hoi' => $bomon['tong_hop']['tong_so_cau_hoi'],
                            'tong_so_de_thi' => $bomon['tong_hop']['tong_so_de_thi']
                        ]);
                        
                        // Thêm chi tiết của bộ môn
                        foreach ($bomon['chitiet'] as $chitiet) {
                            $collection->push([
                                'stt' => $stt++,
                                'nam_hoc' => '',
                                'hoc_ki' => '',
                                'khoa' => '',
                                'bomon' => '',
                                'hoc_phan' => $chitiet['hoc_phan'],
                                'giang_vien' => $chitiet['giang_vien'],
                                'nguoi_phan_bien' => $chitiet['nguoi_phan_bien'],
                                'tong_so_gio' => $chitiet['so_gio'],
                                'tong_so_nguoi' => '',
                                'tong_so_hoc_phan' => '',
                                'tong_so_cau_hoi' => $chitiet['loai_ngan_hang'] == 'Ngân hàng câu hỏi' ? $chitiet['so_luong'] : '',
                                'tong_so_de_thi' => $chitiet['loai_ngan_hang'] == 'Ngân hàng đề thi' ? $chitiet['so_luong'] : ''
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
            'Tổng số giờ',
            'Tổng số GV tham gia',
            'Tổng số học phần',
            'Tổng số câu hỏi',
            'Tổng số đề thi'
        ];
    }

    /**
     * Định dạng styles cho bảng
     */
    public function styles(Worksheet $sheet)
    {
        // Định dạng tiêu đề
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0070C0'], // Màu xanh dương
            ],
        ]);
        
        // Định dạng các hàng dữ liệu
        $dataRows = $sheet->getHighestRow();
        
        // Định dạng các hàng thống kê
        $rowNumber = 2; // Bắt đầu từ hàng 2 (sau tiêu đề)
        while ($rowNumber <= $dataRows) {
            $cellValue = $sheet->getCell('B'.$rowNumber)->getValue();
            
            // Định dạng cho tổng hợp chung
            if (strpos($cellValue, 'TỔNG HỢP CHUNG') !== false) {
                $sheet->getStyle('A'.$rowNumber.':M'.$rowNumber)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFF9900'], // Màu cam
                    ],
                ]);
            }
            // Định dạng cho năm học
            elseif (strpos($cellValue, 'NĂM HỌC:') !== false) {
                $sheet->getStyle('A'.$rowNumber.':M'.$rowNumber)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF92D050'], // Màu xanh lá
                    ],
                ]);
            }
            // Định dạng cho học kỳ
            elseif (strpos($sheet->getCell('C'.$rowNumber)->getValue(), 'HỌC KỲ:') !== false) {
                $sheet->getStyle('A'.$rowNumber.':M'.$rowNumber)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFB4C6E7'], // Màu xanh nhạt
                    ],
                ]);
            }
            // Định dạng cho khoa
            elseif (strpos($sheet->getCell('D'.$rowNumber)->getValue(), 'KHOA:') !== false) {
                $sheet->getStyle('A'.$rowNumber.':M'.$rowNumber)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFD9E1F2'], // Màu tím nhạt
                    ],
                ]);
            }
            // Định dạng cho bộ môn
            elseif (strpos($sheet->getCell('E'.$rowNumber)->getValue(), 'BỘ MÔN:') !== false) {
                $sheet->getStyle('A'.$rowNumber.':M'.$rowNumber)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE2EFDA'], // Màu xanh rêu nhạt
                    ],
                ]);
            }
            
            $rowNumber++;
        }
        
        // Đường viền cho tất cả các ô dữ liệu
        $sheet->getStyle('A1:M'.$dataRows)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Căn giữa cho các cột số liệu
        $sheet->getStyle('A1:A'.$dataRows)->getAlignment()->setHorizontal('center'); // STT
        $sheet->getStyle('I1:M'.$dataRows)->getAlignment()->setHorizontal('center'); // Các cột số liệu
        
        // Tự động điều chỉnh chiều rộng cột
        foreach(range('A','M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return [];
    }
} 