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

class ThongKeGiangVienExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters = [])
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Danh sách giảng viên';
    }

    public function collection()
    {
        $exportData = collect();
        
        // Thêm thông tin bộ lọc
        $exportData->push(['THỐNG KÊ DANH SÁCH GIẢNG VIÊN BIÊN SOẠN VÀ PHẢN BIỆN', '', '', '', '', '']);
        $exportData->push(['Thời gian xuất: ' . date('d/m/Y H:i:s'), '', '', '', '', '']);
        
        // Thông tin bộ lọc
        $filterInfo = 'Bộ lọc: ';
        if (!empty($this->filters['khoa_id'])) {
            $filterInfo .= 'Khoa đã chọn, ';
        } else {
            $filterInfo .= 'Toàn trường, ';
        }
        if (!empty($this->filters['nam_hoc'])) {
            $filterInfo .= 'Năm học: ' . $this->filters['nam_hoc'] . ', ';
        }
        if (!empty($this->filters['hoc_ki'])) {
            $filterInfo .= 'Học kỳ: ' . $this->filters['hoc_ki'];
        }
        $exportData->push([rtrim($filterInfo, ', '), '', '', '', '', '']);
        
        $exportData->push(['', '', '', '', '', '']); // Dòng trống
        
        // Tổng quan
        $exportData->push(['TỔNG QUAN', '', '', '', '', '']);
        $exportData->push(['Tổng số giảng viên:', count($this->data['chi_tiet_vien_chuc']), '', '', '', '']);
        $exportData->push(['Tổng số giờ biên soạn:', $this->getTongGioBienSoan(), '', '', '', '']);
        $exportData->push(['Tổng số giờ phản biện:', $this->getTongGioPhanBien(), '', '', '', '']);
        $exportData->push(['Tổng số giờ:', $this->data['tong_quan']['tong_gio'], '', '', '', '']);
        
        $exportData->push(['', '', '', '', '', '']); // Dòng trống
        
        // Header cho bảng chi tiết
        $exportData->push(['STT', 'Mã giảng viên', 'Tên giảng viên', 'Giờ biên soạn', 'Giờ phản biện', 'Tổng giờ']);
        
        // Dữ liệu chi tiết
        $stt = 1;
        foreach ($this->data['chi_tiet_vien_chuc'] as $vienChuc) {
            $gioBienSoan = 0;
            $gioPhanBien = 0;
            
            foreach ($vienChuc['chi_tiet_hoc_phan'] as $hp) {
                if ($hp['loai'] === 'Biên soạn') {
                    $gioBienSoan += $hp['so_gio'];
                } else {
                    $gioPhanBien += $hp['so_gio'];
                }
            }
            
            $exportData->push([
                $stt++,
                $vienChuc['ma'],
                $vienChuc['ten'],
                $gioBienSoan,
                $gioPhanBien,
                $vienChuc['tong_gio']
            ]);
        }
        
        return $exportData;
    }

    public function headings(): array
    {
        return [];
    }

    private function getTongGioBienSoan()
    {
        $tong = 0;
        foreach ($this->data['chi_tiet_vien_chuc'] as $vienChuc) {
            foreach ($vienChuc['chi_tiet_hoc_phan'] as $hp) {
                if ($hp['loai'] === 'Biên soạn') {
                    $tong += $hp['so_gio'];
                }
            }
        }
        return $tong;
    }

    private function getTongGioPhanBien()
    {
        $tong = 0;
        foreach ($this->data['chi_tiet_vien_chuc'] as $vienChuc) {
            foreach ($vienChuc['chi_tiet_hoc_phan'] as $hp) {
                if ($hp['loai'] === 'Phản biện') {
                    $tong += $hp['so_gio'];
                }
            }
        }
        return $tong;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = 'F';
        
        // Tiêu đề chính
        $sheet->mergeCells('A1:F1');
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
        
        // Thông tin thời gian và bộ lọc
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA']
            ]
        ]);
        
        // Tổng quan
        $sheet->mergeCells('A5:F5');
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ],
            'alignment' => ['horizontal' => 'center']
        ]);
        
        // Header bảng (tìm dòng chứa "STT")
        $headerRow = 0;
        for ($i = 1; $i <= $lastRow; $i++) {
            if ($sheet->getCell('A' . $i)->getValue() === 'STT') {
                $headerRow = $i;
                break;
            }
        }
        
        if ($headerRow > 0) {
            $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray([
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
            
            // Border cho toàn bộ bảng dữ liệu
            $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
            
            // Căn giữa cho cột STT và số liệu
            $sheet->getStyle('A' . ($headerRow + 1) . ':A' . $lastRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('D' . ($headerRow + 1) . ':F' . $lastRow)->getAlignment()->setHorizontal('center');
        }
        
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // STT
            'B' => 15,  // Mã GV
            'C' => 30,  // Tên GV
            'D' => 15,  // Giờ biên soạn
            'E' => 15,  // Giờ phản biện
            'F' => 15,  // Tổng giờ
        ];
    }
} 