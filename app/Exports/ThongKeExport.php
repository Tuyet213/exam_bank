<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ThongKeExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Báo cáo tổng hợp';
    }

    public function collection()
    {
        $exportData = collect();
        
        // Thêm thông tin header
        $exportData->push(['BÁO CÁO TỔNG HỢP - BIÊN SOẠN', '', '', '', '']);
        $exportData->push(['Thời gian xuất: ' . date('d/m/Y H:i:s'), '', '', '', '']);
        $exportData->push(['', '', '', '', '']); // Dòng trống
      
        // Chi tiết theo khoa
        $exportData->push(['CHI TIẾT THEO KHOA', '', '', '', '']);
        $exportData->push(['Loại', 'Tên', 'Tổng học phần', 'Tổng viên chức', 'Tổng giờ']);
        
        foreach ($this->data['chi_tiet_khoa'] as $khoa) {
            $exportData->push([
                'KHOA', $khoa['ten'], 
                $khoa['tong_hoc_phan'],
                $khoa['tong_vien_chuc'],
                $khoa['tong_gio']
            ]);
            
            // Chi tiết bộ môn
            foreach ($khoa['bo_mon'] as $boMon) {
                $exportData->push([
                    '  Bộ môn', $boMon['ten'],
                    $boMon['tong_hoc_phan'],
                    $boMon['tong_vien_chuc'],
                    $boMon['tong_gio']
                ]);
                
                // Chi tiết học phần
                foreach ($boMon['hoc_phan'] as $hocPhan) {
                    $exportData->push([
                        '    Học phần', $hocPhan['ten'] . ' (' . $hocPhan['ma'] . ')',
                        1,
                        count($hocPhan['vien_chuc']),
                        array_sum(array_column($hocPhan['vien_chuc'], 'so_gio'))
                    ]);
                }
            }
            
            $exportData->push(['', '', '', '', '']); // Dòng trống giữa các khoa
        }
        
        // Chi tiết viên chức
        $exportData->push(['CHI TIẾT VIÊN CHỨC', '', '', '', '']);
        $exportData->push(['Mã viên chức', 'Tên viên chức', 'Tổng giờ', 'Số học phần', 'Chi tiết']);
        
        foreach ($this->data['chi_tiet_vien_chuc'] as $vienChuc) {
            $exportData->push([
                $vienChuc['ma'],
                $vienChuc['ten'],
                $vienChuc['tong_gio'],
                count($vienChuc['chi_tiet_hoc_phan']),
                ''
            ]);
            
            // Chi tiết học phần của viên chức
            foreach ($vienChuc['chi_tiet_hoc_phan'] as $hp) {
                $exportData->push([
                    '',
                    '  ' . $hp['ten_hoc_phan'],
                    $hp['so_gio'],
                    $hp['loai'],
                    $hp['khoa'] . ' - ' . $hp['bo_mon']
                ]);
            }
        }
        
        return $exportData;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Tiêu đề chính
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E86AB']
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center'
            ]
        ]);
        
        // Thông tin thời gian
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA']
            ]
        ]);
        
        // Tìm và style các header
        for ($i = 1; $i <= $lastRow; $i++) {
            $cellValue = $sheet->getCell('A' . $i)->getValue();
            
            if (in_array($cellValue, ['TỔNG QUAN', 'CHI TIẾT THEO KHOA', 'CHI TIẾT VIÊN CHỨC'])) {
                $sheet->mergeCells('A' . $i . ':E' . $i);
                $sheet->getStyle('A' . $i)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E3F2FD']
                    ],
                    'alignment' => ['horizontal' => 'center']
                ]);
            }
            
            if ($cellValue === 'Loại' || $cellValue === 'Mã viên chức') {
                $sheet->getStyle('A' . $i . ':E' . $i)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1976D2']
                    ],
                    'alignment' => ['horizontal' => 'center']
                ]);
            }
            
            if ($cellValue === 'KHOA') {
                $sheet->getStyle('A' . $i . ':E' . $i)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BBDEFB']
                    ]
                ]);
            }
        }
        
        // Border cho toàn bộ dữ liệu
        $sheet->getStyle('A1:E' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 40,
            'C' => 15,
            'D' => 15,
            'E' => 15,
        ];
    }
} 