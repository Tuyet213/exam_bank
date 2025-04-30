<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ThongKeGiangVienExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $thongke_data;

    public function __construct($thongke_data)
    {
        $this->thongke_data = $thongke_data;
    }

    public function array(): array
    {
        $data = [];
        
        // Thêm dòng tổng hợp chung
        $data[] = [
            'TỔNG HỢP CHUNG',
            '',
            ''
        ];
        
        $data[] = [
            'Tổng số giảng viên tham gia',
            $this->thongke_data['tong_hop']['tong_so_giang_vien'],
            ''
        ];
        
        // Thêm dòng trống
        $data[] = ['', '', ''];
        
        // Thêm tiêu đề chi tiết
        $data[] = [
            'CHI TIẾT THEO CẤU TRÚC PHÂN CẤP',
            '',
            ''
        ];

        // Duyệt qua cấu trúc phân cấp
        foreach ($this->thongke_data['giang_vien'] as $nam_hoc => $nam_hoc_data) {
            // Thêm thông tin năm học
            $data[] = [
                "NĂM HỌC: {$nam_hoc}",
                "Số giảng viên: {$nam_hoc_data['tong_so_giang_vien']}",
                ''
            ];

            foreach ($nam_hoc_data['hoc_ki'] as $hoc_ki => $hoc_ki_data) {
                // Thêm thông tin học kỳ
                $data[] = [
                    "    HỌC KỲ: {$hoc_ki}",
                    "Số giảng viên: {$hoc_ki_data['tong_so_giang_vien']}",
                    ''
                ];

                foreach ($hoc_ki_data['khoa'] as $khoa_id => $khoa_data) {
                    // Thêm thông tin khoa
                    $data[] = [
                        "        KHOA: {$khoa_id}",
                        "Số giảng viên: {$khoa_data['tong_so_giang_vien']}",
                        ''
                    ];

                    foreach ($khoa_data['bo_mon'] as $bo_mon_id => $bo_mon_data) {
                        // Thêm thông tin bộ môn
                        $data[] = [
                            "            BỘ MÔN: {$bo_mon_id}",
                            "Số giảng viên: {$bo_mon_data['tong_so_giang_vien']}",
                            ''
                        ];

                        // Thêm thông tin chi tiết từng giảng viên
                        foreach ($bo_mon_data['giang_vien'] as $gv) {
                            // Tính tổng số giờ của giảng viên
                            $tong_so_gio = array_sum(array_column($gv['chi_tiet_mon'], 'so_gio'));
                            
                            // Thêm tên giảng viên và tổng số giờ
                            $data[] = [
                                "                {$gv['ten']} (Tổng: {$tong_so_gio} giờ)",
                                '',
                                ''
                            ];

                            // Thêm danh sách học phần
                            $ds_hoc_phan = [];
                            foreach ($gv['chi_tiet_mon'] as $mon) {
                                $data[] = [
                                    "                    - {$mon['ten_mon']} ({$mon['so_gio']} giờ)",
                                    '',
                                    ''
                                ];
                            }
                        }

                        $data[] = ['', '', '']; // Dòng trống sau mỗi bộ môn
                    }
                }
            }
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'THỐNG KÊ GIẢNG VIÊN THAM GIA BIÊN SOẠN',
            '',
            ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Style cho tiêu đề
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Style cho tổng hợp chung
        $sheet->mergeCells('A2:C2');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        
        // Style cho tiêu đề chi tiết
        $sheet->mergeCells('A4:C4');
        $sheet->getStyle('A4')->getFont()->setBold(true);
        
        // Style cho toàn bộ bảng
        $sheet->getStyle("A1:C{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle("A1:C{$lastRow}")->getAlignment()->setVertical('center');
        
        // Tự động điều chỉnh độ rộng cột
        foreach (range('A', 'C') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
} 