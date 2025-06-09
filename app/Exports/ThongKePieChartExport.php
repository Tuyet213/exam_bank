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

class ThongKePieChartExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
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
        return 'Dữ liệu biểu đồ tròn';
    }

    public function collection()
    {
        $exportData = collect();
        
        // Thêm thông tin header
        $exportData->push(['DỮ LIỆU CHO BIỂU ĐỒ TRÒN - BIÊN SOẠN NGÂN HÀNG CÂU HỎI', '', '', '', '']);
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
       
        
        // Dữ liệu cho biểu đồ tròn
        $exportData->push(['DỮ LIỆU BIỂU ĐỒ TRÒN', '', '', '', '']);
        $exportData->push(['Tên', 'Số học phần', 'Tỷ lệ %', '', '']);
        
        $total = array_sum(array_column($this->chartData['pie_chart'], 'value'));
        foreach ($this->chartData['pie_chart'] as $item) {
            $percentage = $total > 0 ? round(($item['value'] / $total) * 100, 1) : 0;
            $exportData->push([
                $item['label'],
                $item['value'],
                $percentage . '%',
                '',
                ''
            ]);
        }
        
        $exportData->push(['', '', '', '', '']); // Dòng trống
        
        // Phân tích chi tiết
        $exportData->push(['PHÂN TÍCH CHI TIẾT', '', '', '', '']);
        
        if (!empty($this->filters['khoa_id']) && !empty($this->filters['bomon_id'])) {
            // Hiển thị chi tiết học phần của bộ môn
            $khoa = $this->data['chi_tiet_khoa'][$this->filters['khoa_id']] ?? null;
            if ($khoa && isset($khoa['bo_mon'][$this->filters['bomon_id']])) {
                $boMon = $khoa['bo_mon'][$this->filters['bomon_id']];
                $exportData->push(['Bộ môn: ' . $boMon['ten'], '', '', '', '']);
                $exportData->push(['Học phần', 'Số viên chức', 'Tổng giờ', 'Trạng thái', '']);
                
                foreach ($boMon['hoc_phan'] as $hocPhan) {
                    $tongGio = array_sum(array_column($hocPhan['vien_chuc'], 'so_gio'));
                    $exportData->push([
                        $hocPhan['ten'],
                        count($hocPhan['vien_chuc']),
                        $tongGio,
                        $hocPhan['trang_thai'] ?? 'Không xác định',
                        ''
                    ]);
                }
            }
        } elseif (!empty($this->filters['khoa_id'])) {
            // Hiển thị chi tiết bộ môn của khoa
            $khoa = $this->data['chi_tiet_khoa'][$this->filters['khoa_id']] ?? null;
            if ($khoa) {
                $exportData->push(['Khoa: ' . $khoa['ten'], '', '', '', '']);
                $exportData->push(['Bộ môn', 'Số học phần', 'Số viên chức', 'Tổng giờ', '']);
                
                foreach ($khoa['bo_mon'] as $boMon) {
                    $exportData->push([
                        $boMon['ten'],
                        $boMon['tong_hoc_phan'],
                        $boMon['tong_vien_chuc'],
                        $boMon['tong_gio'],
                        ''
                    ]);
                }
            }
        } else {
            // Hiển thị chi tiết theo khoa
            $exportData->push(['Phân bố theo khoa', '', '', '', '']);
            $exportData->push(['Khoa', 'Số học phần', 'Số viên chức', 'Tổng giờ', '']);
            
            foreach ($this->data['chi_tiet_khoa'] as $khoa) {
                $exportData->push([
                    $khoa['ten'],
                    $khoa['tong_hoc_phan'],
                    $khoa['tong_vien_chuc'],
                    $khoa['tong_gio'],
                    ''
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
            
            if (in_array($cellValue, ['TỔNG QUAN', 'DỮ LIỆU BIỂU ĐỒ TRÒN', 'PHÂN TÍCH CHI TIẾT', 'HƯỚNG DẪN TẠO BIỂU ĐỒ TRÒN:'])) {
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
            
            if ($cellValue === 'Tên' || $cellValue === 'Khoa' || $cellValue === 'Bộ môn' || $cellValue === 'Học phần') {
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
                
                // Tìm dòng cuối của bảng để vẽ border
                $tableEndRow = $i;
                for ($j = $i + 1; $j <= $lastRow; $j++) {
                    if (trim($sheet->getCell('A' . $j)->getValue()) === '') {
                        break;
                    }
                    $tableEndRow = $j;
                }
                
                // Border cho bảng
                $sheet->getStyle('A' . $i . ':D' . $tableEndRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
            }
            
            if (strpos($cellValue, 'Khoa:') === 0 || strpos($cellValue, 'Bộ môn:') === 0) {
                $sheet->getStyle('A' . $i . ':D' . $i)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BBDEFB']
                    ]
                ]);
            }
            
            // Style cho hướng dẫn
            if (strpos($cellValue, '1.') === 0 || strpos($cellValue, '2.') === 0 || strpos($cellValue, '3.') === 0 || strpos($cellValue, '4.') === 0) {
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
            'B' => 15,  // Số học phần/Số viên chức
            'C' => 15,  // Tỷ lệ %/Tổng giờ
            'D' => 20,  // Trạng thái
            'E' => 10,  // Cột trống
        ];
    }
} 