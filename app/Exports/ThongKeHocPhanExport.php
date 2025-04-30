<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ThongKeHocPhanExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
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
            'Tổng số học phần',
            $this->thongke_data['tong_hop']['tong_so_hoc_phan'],
            ''
        ];
        
        $data[] = [
            'Tổng số giảng viên tham gia',
            $this->thongke_data['tong_hop']['tong_so_giang_vien'],
            ''
        ];
        
        $data[] = ['', '', '']; // Dòng trống
        
        // Thêm dữ liệu chi tiết theo cấu trúc phân cấp
        foreach ($this->thongke_data['nam_hoc'] as $nam_hoc => $nam_hoc_data) {
            $data[] = [
                "NĂM HỌC $nam_hoc",
                '',
                ''
            ];
            
            foreach ($nam_hoc_data['hoc_ki'] as $hoc_ki => $hoc_ki_data) {
                $data[] = [
                    "Học kỳ $hoc_ki",
                    '',
                    ''
                ];
                
                foreach ($hoc_ki_data['khoa'] as $khoa_id => $khoa_data) {
                    $data[] = [
                        $khoa_data['ten'],
                        '',
                        ''
                    ];
                    
                    foreach ($khoa_data['bo_mon'] as $bo_mon_id => $bo_mon_data) {
                        $data[] = [
                            '- ' . $bo_mon_data['ten'],
                            '',
                            ''
                        ];
                        
                       
                        
                        // Thêm danh sách học phần
                        foreach ($bo_mon_data['hoc_phan'] as $hp) {
                            $giang_vien_str = '';
                            foreach ($hp['giang_vien'] as $gv) {
                                $giang_vien_str .= $gv['ten'] . ' (' . $gv['so_gio'] . ' giờ), ';
                            }
                            $giang_vien_str = rtrim($giang_vien_str, ', ');
                            
                            $data[] = [
                                $hp['ten'],
                                $hp['ma_hoc_phan'],
                                $giang_vien_str
                            ];
                        }
                        
                        $data[] = ['', '', '']; // Dòng trống
                    }
                }
            }
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'THỐNG KÊ HỌC PHẦN ĐƯỢC BIÊN SOẠN',
            '',
            ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]], // Tiêu đề chính
            2 => ['font' => ['bold' => true]], // Tiêu đề "TỔNG HỢP CHUNG"
            'A' => ['font' => ['bold' => true]], // Cột đầu tiên
        ];
    }
} 