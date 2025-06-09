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

class ThongKeBarChartExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;
    protected $chartData;
    protected $filters;

    public function __construct($data, $chartData, $filters = [])
    {
        $this->data = $data;
        $this->chartData = $chartData;
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Dữ liệu biểu đồ cột';
    }

    public function collection()
    {
        $exportData = collect();
        
        // Thêm thông tin header
        $exportData->push(['DỮ LIỆU CHO BIỂU ĐỒ CỘT - BIÊN SOẠN NGÂN HÀNG CÂU HỎI', '', '', '', '']);
        $exportData->push(['Thời gian xuất: ' . date('d/m/Y H:i:s'), '', '', '', '']);
        
        // Thông tin bộ lọc
        $filterInfo = 'Bộ lọc: ';
        if (!empty($this->filters['khoa_id'])) {
            $filterInfo .= 'Khoa đã chọn';
            if (!empty($this->filters['bomon_id'])) {
                $filterInfo .= ' → Bộ môn đã chọn';
            }
        } else {
            $filterInfo .= 'Toàn trường';
        }
        if (!empty($this->filters['nam_hoc'])) {
            $filterInfo .= ', Năm học: ' . $this->filters['nam_hoc'];
        }
        if (!empty($this->filters['hoc_ki'])) {
            $filterInfo .= ', Học kỳ: ' . $this->filters['hoc_ki'];
        }
        $exportData->push([$filterInfo, '', '', '', '']);
        
        $exportData->push(['', '', '', '', '']); // Dòng trống
        
        // Tổng quan
        $exportData->push(['TỔNG QUAN', '', '', '', '']);
        $exportData->push(['Tổng học phần:', $this->data['tong_quan']['tong_hoc_phan'], '', '', '']);
        $exportData->push(['Tổng viên chức:', $this->data['tong_quan']['tong_vien_chuc'], '', '', '']);
        $exportData->push(['Tổng số giờ:', $this->data['tong_quan']['tong_gio'], '', '', '']);
        
        $exportData->push(['', '', '', '', '']); // Dòng trống
        
        
        // Dữ liệu cho biểu đồ
        $exportData->push(['DỮ LIỆU BIỂU ĐỒ', '', '', '', '']);
        $exportData->push(['Tên', 'Số học phần', 'Số viên chức', 'Tổng giờ', '']);
        
        foreach ($this->chartData['bar_chart'] as $item) {
            $exportData->push([
                $item['name'],
                $item['hoc_phan'],
                $item['vien_chuc'],
                $item['gio'],
                ''
            ]);
        }
        
        $exportData->push(['', '', '', '', '']); // Dòng trống
        
        // Chi tiết theo cấu trúc
        $exportData->push(['CHI TIẾT THEO CẤU TRÚC', '', '', '', '']);
        
        foreach ($this->data['chi_tiet_khoa'] as $khoaId => $khoa) {
            $exportData->push(['KHOA: ' . $khoa['ten'], $khoa['tong_hoc_phan'], $khoa['tong_vien_chuc'], $khoa['tong_gio'], '']);
            
            foreach ($khoa['bo_mon'] as $boMonId => $boMon) {
                $exportData->push(['  Bộ môn: ' . $boMon['ten'], $boMon['tong_hoc_phan'], $boMon['tong_vien_chuc'], $boMon['tong_gio'], '']);
                
                foreach ($boMon['hoc_phan'] as $hocPhanId => $hocPhan) {
                    $tongGioHocPhan = array_sum(array_column($hocPhan['vien_chuc'], 'so_gio'));
                    $exportData->push(['    Học phần: ' . $hocPhan['ten'], '1', count($hocPhan['vien_chuc']), $tongGioHocPhan, '']);
                }
            }
            $exportData->push(['', '', '', '', '']); // Dòng trống giữa các khoa
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
        
        // Thông tin thời gian và bộ lọc
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA']
            ]
        ]);
        
        // Tìm và style các header
        for ($i = 1; $i <= $lastRow; $i++) {
            $cellValue = $sheet->getCell('A' . $i)->getValue();
            
            if (in_array($cellValue, ['TỔNG QUAN', 'DỮ LIỆU BIỂU ĐỒ', 'CHI TIẾT THEO CẤU TRÚC', 'HƯỚNG DẪN TẠO BIỂU ĐỒ:'])) {
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
            
            if ($cellValue === 'Tên') {
                $sheet->getStyle('A' . $i . ':D' . $i)->applyFromArray([
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
                
                // Border cho bảng dữ liệu biểu đồ
                $dataEndRow = $i + count($this->chartData['bar_chart']);
                $sheet->getStyle('A' . $i . ':D' . $dataEndRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
            }
            
            if (strpos($cellValue, 'KHOA:') === 0) {
                $sheet->getStyle('A' . $i . ':D' . $i)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BBDEFB']
                    ]
                ]);
            }
            
            // Style cho hướng dẫn
            if (strpos($cellValue, '1.') === 0 || strpos($cellValue, '2.') === 0 || strpos($cellValue, '3.') === 0) {
                $sheet->getStyle('A' . $i)->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF3E0']
                    ]
                ]);
            }
        }
        
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 50,  // Tên
            'B' => 15,  // Số học phần
            'C' => 15,  // Số viên chức
            'D' => 15,  // Tổng giờ
            'E' => 10,  // Cột trống
        ];
    }
} 