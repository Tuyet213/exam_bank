<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Cell;
use PhpOffice\PhpWord\Style\Table;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\SimpleType\TblWidth;

// Tạo một instance của PhpWord
$phpWord = new PhpWord();

// Thiết lập các style mặc định
$phpWord->setDefaultFontName('Times New Roman');
$phpWord->setDefaultFontSize(12);

// Thêm section vào tài liệu
$section = $phpWord->addSection();

// Style cho bảng
$tableStyle = [
    'borderSize' => 1,
    'borderColor' => '000000',
    'cellMargin' => 100,
    'alignment' => JcTable::CENTER
];

// Style cho cell tiêu đề
$headerCellStyle = [
    'valign' => 'center',
    'bgColor' => 'FFFFFF',
];

// Style cho nội dung cell
$cellStyle = [
    'valign' => 'top',
];

// Style cho text đậm
$boldTextStyle = [
    'bold' => true,
];

// Style cho tiêu đề
$titleStyle = [
    'bold' => true,
    'alignment' => 'center',
    'spaceAfter' => 0,
];

$subtitleStyle = [
    'italic' => true,
    'alignment' => 'center',
    'spaceAfter' => 100,
];

// Thêm thông tin trường
$headerTable = $section->addTable();
$headerTable->addRow();
$headerTable->addCell(4000)->addText('TRƯỜNG ĐẠI HỌC NHA TRANG', $boldTextStyle);
$headerTable->addRow();
$headerTable->addCell(4000)->addText('KHOA/VIỆN: .............................', $boldTextStyle);
$headerTable->addRow();
$headerTable->addCell(4000)->addText('BỘ MÔN: ..................................', $boldTextStyle);

// Thêm tiêu đề
$section->addTextBreak(1);
$section->addText('BẢNG NGÂN HÀNG CÂU HỎI THI, ĐÁP ÁN VÀ THANG ĐIỂM', $titleStyle);
$section->addText('(Dùng cho câu hỏi thi trắc nghiệm)', $subtitleStyle);
$section->addTextBreak(1);

// Thêm thông tin HP
$section->addText('Tên HP: ..........................................................................................................................');
$section->addText('Tác giả biên soạn NHCHT và đáp án: ..................................................................................');
$section->addTextBreak(1);

// Tạo nút + (không có trong word nhưng giữ lại để giống mẫu)
$section->addText('+', ['bold' => true, 'borderSize' => 1, 'borderColor' => '000000']);
$section->addTextBreak(1);

// Tạo bảng chính (MỘT BẢNG THỐNG NHẤT)
$table = $section->addTable($tableStyle);

// Hàng tiêu đề
$table->addRow();
$table->addCell(1000, $headerCellStyle)->addText('Câu hỏi', $boldTextStyle);
$table->addCell(4000, $headerCellStyle)->addText('Nội dung', $boldTextStyle);
$table->addCell(1500, $headerCellStyle)->addText('Điểm', $boldTextStyle);
$table->addCell(1500, $headerCellStyle)->addText('Độ khó (Dễ, Trung bình, Khó)', $boldTextStyle);

// Các chương/chủ đề theo thứ tự La Mã
$chapters = ['I', 'II', 'III', 'IV'];

foreach ($chapters as $index => $chapter) {
    // Tiêu đề chương/chủ đề
    $table->addRow();
    $cell = $table->addCell(8000, ['gridSpan' => 4]);
    $cell->addText("$chapter. Chương/Chủ đề", $boldTextStyle);
    
    // Chuẩn đầu ra 1
    $table->addRow();
    $cell = $table->addCell(8000, ['gridSpan' => 4]);
    $cell->addText('Chuẩn đầu ra 1, Số lượng câu hỏi: ...................................');
    
    // Câu hỏi 1
    $table->addRow();
    $table->addCell(1000, ['vMerge' => 'restart', 'valign' => 'center'])->addText('1', ['alignment' => 'center']);
    $table->addCell(4000, $cellStyle)->addText('Câu hỏi: .........................................');
    $table->addCell(1500, $cellStyle)->addText('..........d');
    $table->addCell(1500, $cellStyle)->addText('.........');
    
    // Đáp án câu 1
    $table->addRow();
    $table->addCell(1000, ['vMerge' => 'continue']);
    $table->addCell(4000, $cellStyle)->addText('Đáp án:');
    $table->addCell(1500, $cellStyle);
    $table->addCell(1500, $cellStyle);
    
    // Các lựa chọn trắc nghiệm
    $options = ['A', 'B', 'C', 'D'];
    foreach ($options as $option) {
        $table->addRow();
        $table->addCell(1000, ['vMerge' => 'continue']);
        $table->addCell(4000, $cellStyle)->addText("$option. .......................................");
        $table->addCell(1500, $cellStyle)->addText('..........d');
        $table->addCell(1500, $cellStyle);
    }
    
    // Câu hỏi 2
    $table->addRow();
    $table->addCell(1000, ['vMerge' => 'restart', 'valign' => 'center'])->addText('2', ['alignment' => 'center']);
    $table->addCell(4000, $cellStyle)->addText('Câu hỏi: .........................................');
    $table->addCell(1500, $cellStyle)->addText('..........d');
    $table->addCell(1500, $cellStyle)->addText('.........');
    
    $table->addRow();
    $table->addCell(1000, ['vMerge' => 'continue']);
    $table->addCell(4000, $cellStyle)->addText('Đáp án:');
    $table->addCell(1500, $cellStyle);
    $table->addCell(1500, $cellStyle);
    
    // Các lựa chọn trắc nghiệm cho câu 2
    foreach ($options as $option) {
        $table->addRow();
        $table->addCell(1000, ['vMerge' => 'continue']);
        $table->addCell(4000, $cellStyle)->addText("$option. .......................................");
        $table->addCell(1500, $cellStyle)->addText('..........d');
        $table->addCell(1500, $cellStyle);
    }
    
    // Chuẩn đầu ra 2 (nếu không phải chương cuối)
    if ($index < count($chapters) - 1) {
        $table->addRow();
        $cell = $table->addCell(8000, ['gridSpan' => 4]);
        $cell->addText('Chuẩn đầu ra 2, Số lượng câu hỏi: ...................................');
        
        // Câu hỏi 1
        $table->addRow();
        $table->addCell(1000, ['vMerge' => 'restart', 'valign' => 'center'])->addText('1', ['alignment' => 'center']);
        $table->addCell(4000, $cellStyle)->addText('Câu hỏi: .........................................');
        $table->addCell(1500, $cellStyle)->addText('..........d');
        $table->addCell(1500, $cellStyle)->addText('.........');
        
        // Đáp án câu 1
        $table->addRow();
        $table->addCell(1000, ['vMerge' => 'continue']);
        $table->addCell(4000, $cellStyle)->addText('Đáp án:');
        $table->addCell(1500, $cellStyle);
        $table->addCell(1500, $cellStyle);
        
        // Các lựa chọn trắc nghiệm
        foreach ($options as $option) {
            $table->addRow();
            $table->addCell(1000, ['vMerge' => 'continue']);
            $table->addCell(4000, $cellStyle)->addText("$option. .......................................");
            $table->addCell(1500, $cellStyle)->addText('..........d');
            $table->addCell(1500, $cellStyle);
        }
        
        // Câu hỏi 2
        $table->addRow();
        $table->addCell(1000, ['vMerge' => 'restart', 'valign' => 'center'])->addText('2', ['alignment' => 'center']);
        $table->addCell(4000, $cellStyle)->addText('Câu hỏi: .........................................');
        $table->addCell(1500, $cellStyle)->addText('..........d');
        $table->addCell(1500, $cellStyle)->addText('.........');
        
        $table->addRow();
        $table->addCell(1000, ['vMerge' => 'continue']);
        $table->addCell(4000, $cellStyle)->addText('Đáp án:');
        $table->addCell(1500, $cellStyle);
        $table->addCell(1500, $cellStyle);
        
        // Các lựa chọn trắc nghiệm cho câu 2
        foreach ($options as $option) {
            $table->addRow();
            $table->addCell(1000, ['vMerge' => 'continue']);
            $table->addCell(4000, $cellStyle)->addText("$option. .......................................");
            $table->addCell(1500, $cellStyle)->addText('..........d');
            $table->addCell(1500, $cellStyle);
        }
    }
}

// Tổng điểm
$table->addRow();
$table->addCell(5000, ['gridSpan' => 2, 'valign' => 'center'])->addText('Tổng điểm', ['alignment' => 'center']);
$table->addCell(1500, $cellStyle)->addText('..........d');
$table->addCell(1500, $cellStyle);

// Thêm phần ký
$section->addTextBreak(1);
$section->addText('TRƯỞNG BM/KHOA/VIỆN', ['alignment' => 'right', 'bold' => true]);
$section->addText('(Ký và ghi rõ họ tên)', ['alignment' => 'right', 'italic' => true]);

// Lưu file
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('mau_bang_ngan_hang_trac_nghiem.docx');

echo "Đã tạo file mẫu trắc nghiệm thành công!";
?> 