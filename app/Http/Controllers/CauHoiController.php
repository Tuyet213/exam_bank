<?php

namespace App\Http\Controllers;

use App\Models\CauHoi;
use App\Models\CTDSDangKy;
use App\Models\HocPhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CauHoiController extends Controller
{
    public function danhSachHocPhan(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->pluck('name');
        $role = 'user';
        
        if($roles->contains('Giảng viên')){
            $role = 'gv';
        }
        elseif($roles->contains('Trưởng Bộ Môn')){
            $role = 'tbm';
        }
        elseif($roles->contains('Nhân viên P.ĐBCL')){
            $role = 'dbcl';
        }
        elseif($roles->contains('Admin')){
            $role = 'admin';
        }
        
        $query = CTDSDangKy::whereHas('dsGvBienSoans', function($query) use ($user) {
                $query->where('id_vien_chuc', $user->id);
            })
            ->where('loai_ngan_hang', 1) // Ngân hàng câu hỏi thi
            ->with(['hocPhan', 'dsDangKy']);

        // Lọc theo tên/mã học phần
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('hocPhan', function($q) use ($search) {
                $q->where('ten', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Lọc theo năm học
        if ($request->filled('nam_hoc')) {
            $query->whereHas('dsDangKy', function($q) use ($request) {
                $q->where('nam_hoc', $request->nam_hoc);
            });
        }

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky')) {
            $query->whereHas('dsDangKy', function($q) use ($request) {
                $q->where('hoc_ki', $request->hoc_ky);
            });
        }

        // Lọc theo hình thức thi
        if ($request->filled('hinh_thuc_thi')) {
            $query->where('hinh_thuc_thi', $request->hinh_thuc_thi);
        }

        $ctdsdangkies = $query->get();

        return Inertia::render('CauHoi/DanhSachHocPhan', [
            'ctdsdangkies' => $ctdsdangkies,
            'role' => $role,
            'filters' => $request->only(['search', 'nam_hoc', 'hoc_ky', 'hinh_thuc_thi'])
        ]);
    }

    public function danhSachCauHoi($id, Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->pluck('name');
        $role = 'user';
        
        if($roles->contains('Giảng viên')){
            $role = 'gv';
        }
        elseif($roles->contains('Trưởng Bộ Môn')){
            $role = 'tbm';
        }
        elseif($roles->contains('Nhân viên P.ĐBCL')){
            $role = 'dbcl';
        }
        elseif($roles->contains('Admin')){
            $role = 'admin';
        }
        
        $ctDangKy = CTDSDangKy::with(['hocPhan', 'hocPhan.chuongs', 'hocPhan.chuanDauRas'])->findOrFail($id);
        $query = CauHoi::with(['chuong', 'chuanDauRa'])->where('id_ct_ds_dang_ky', $id);

        // Lọc theo nội dung câu hỏi
        if ($request->filled('cau_hoi')) {
            $query->where('cau_hoi', 'like', '%' . $request->cau_hoi . '%');
        }
        // Lọc theo mức độ
        if ($request->filled('muc_do')) {
            $query->where('muc_do', $request->muc_do);
        }
        // Lọc theo chương
        if ($request->filled('id_chuong')) {
            $query->where('id_chuong', $request->id_chuong);
        }
        // Lọc theo chuẩn đầu ra
        if ($request->filled('id_chuan_dau_ra')) {
            $query->where('id_chuan_dau_ra', $request->id_chuan_dau_ra);
        }

        $cauHois = $query->get();

        return Inertia::render('CauHoi/DanhSach', [
            'ctDangKy' => $ctDangKy,
            'cauHois' => $cauHois,
            'role' => $role,
            'filters' => $request->only(['cau_hoi', 'muc_do', 'id_chuong', 'id_chuan_dau_ra'])
        ]);
    }

    public function tao($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->pluck('name');
        $role = 'user';
        
        if($roles->contains('Giảng viên')){
            $role = 'gv';
        }
        elseif($roles->contains('Trưởng Bộ Môn')){
            $role = 'tbm';
        }
        
        $ctDangKy = CTDSDangKy::with(['hocPhan'])->findOrFail($id);
        
        // Lấy danh sách chuẩn đầu ra và chương của học phần
        $chuanDauRas = \App\Models\ChuanDauRa::where('id_hoc_phan', $ctDangKy->hocPhan->id)
            ->where('able', true)
            ->get();
            
        $chuongs = \App\Models\Chuong::where('id_hoc_phan', $ctDangKy->hocPhan->id)
            ->where('able', true)
            ->get();

        return Inertia::render('CauHoi/Tao', [
            'ctDangKy' => $ctDangKy,
            'chuanDauRas' => $chuanDauRas,
            'chuongs' => $chuongs,
            'role' => $role
        ]);
    }

    public function luu(Request $request)
    {
        $validated = $request->validate([
            'cau_hoi' => 'required|string',
            'id_ct_ds_dang_ky' => 'required|exists:c_t_d_s_dang_kies,id',
            'id_chuan_dau_ra' => 'required|exists:chuan_dau_ras,id',
            'id_chuong' => 'required|exists:chuongs,id',
            'diem' => 'required|numeric|min:0',
            'phan_loai' => 'required|in:0,1,2', // 0: Trắc nghiệm, 1: Tự luận/vấn đáp, 2: tự luận
            'muc_do' => 'required|in:1,2,3', // 1: dễ, 2: trung bình, 3: khó
            'dap_ans' => 'required_if:phan_loai,0|array', // Bắt buộc nếu là câu hỏi trắc nghiệm
            'dap_ans.*.noi_dung' => 'required_if:phan_loai,0|string',
            'dap_ans.*.trang_thai' => 'required_if:phan_loai,0|boolean',
            'dap_ans.*.diem' => 'required_if:phan_loai,0|numeric|min:0',
        ]);

        // Tính tổng điểm từ các đáp án nếu là trắc nghiệm
        if ($request->phan_loai == 0) {
            $tongDiem = 0;
            foreach ($request->dap_ans as $dapAn) {
                if (isset($dapAn['trang_thai']) && $dapAn['trang_thai']) {
                    $tongDiem += $dapAn['diem'];
                }
            }
            // Cập nhật lại điểm cho câu hỏi
            $validated['diem'] = $tongDiem;
        }

        $cauHoi = CauHoi::create($validated);

        foreach ($request->dap_ans as $dapAn) {
            $cauHoi->dapAns()->create([
                'dap_an' => $dapAn['noi_dung'],
                'trang_thai' => $dapAn['trang_thai'],
                'diem' => $dapAn['diem']
            ]);
        }
        return redirect()->route('cauhoi.danhsach', $request->id_ct_ds_dang_ky)
            ->with('success', 'Tạo câu hỏi thành công');
    }

    public function import($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->pluck('name');
        $role = 'user';
        
        if($roles->contains('Giảng viên')){
            $role = 'gv';
        }
        elseif($roles->contains('Trưởng Bộ Môn')){
            $role = 'tbm';
        }
        elseif($roles->contains('Nhân viên P.ĐBCL')){
            $role = 'dbcl';
        }
        elseif($roles->contains('Admin')){
            $role = 'admin';
        }
        
        $ctDangKy = CTDSDangKy::with(['hocPhan'])->findOrFail($id);
        
        return Inertia::render('CauHoi/Import', [
            'ctDangKy' => $ctDangKy,
            'role' => $role,
            'downloadTemplateUrl' => route('cauhoi.download_template', $id)
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:docx,doc',
            'id_ct_ds_dang_ky' => 'required|exists:c_t_d_s_dang_kies,id',
        ]);

        Log::info('Bắt đầu upload file ngân hàng câu hỏi', [
            'id_ct_ds_dang_ky' => $request->id_ct_ds_dang_ky,
            'file_name' => $request->file('file')->getClientOriginalName(),
        ]);

        $ctDangKy = CTDSDangKy::with([
            'hocPhan', 'hocPhan.chuongs', 'hocPhan.chuanDauRas',
            'dsGvBienSoans.vienChuc', 'dsDangKy'
        ])->findOrFail($request->id_ct_ds_dang_ky);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $filePath = $file->getRealPath();
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            Log::info('Đã load file Word thành công');

            $fileInfo = [
                'isTracNghiem' => null,
                'tenHocPhan' => '',
                'giangVienBienSoan' => '',
                'chuongHienTai' => null,
                'chuanDauRaHienTai' => null
            ];
            $questions = [];
            $currentQuestion = null;
            $errorMessages = [];

            // Quét toàn bộ text (ngoài bảng và trong bảng)
            $allTexts = [];
            foreach ($phpWord->getSections() as $sectionIndex => $section) {
                foreach ($section->getElements() as $elementIndex => $element) {
                    if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                        $text = '';
                        foreach ($element->getElements() as $textElement) {
                            if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                                $text .= $textElement->getText();
                            }
                        }
                        $text = trim($text);
                        if ($text) $allTexts[] = $text;
                    } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                        foreach ($element->getRows() as $row) {
                            foreach ($row->getCells() as $cell) {
                                foreach ($cell->getElements() as $cellElement) {
                                    if ($cellElement instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                        $text = '';
                                        foreach ($cellElement->getElements() as $textElement) {
                                            if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                                                $text .= $textElement->getText();
                                            }
                                        }
                                        $text = trim($text);
                                        if ($text) $allTexts[] = $text;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            foreach ($allTexts as $text) {
                if ($fileInfo['isTracNghiem'] === null) {
                    if (stripos($text, 'Dùng cho câu hỏi thi trắc nghiệm') !== false) {
                        $fileInfo['isTracNghiem'] = true;
                        Log::info('Đã xác định: File chứa câu hỏi TRẮC NGHIỆM');
                    } elseif (stripos($text, 'Dùng cho câu hỏi thi tự luận') !== false) {
                        $fileInfo['isTracNghiem'] = false;
                        Log::info('Đã xác định: File chứa câu hỏi TỰ LUẬN');
                    }
                }
                if (empty($fileInfo['tenHocPhan']) && preg_match('/^Tên HP:\s*(.+)$/i', $text, $matches)) {
                    $fileInfo['tenHocPhan'] = trim($matches[1]);
                    Log::info('Đã tìm thấy tên học phần: ' . $fileInfo['tenHocPhan']);
                }
                if (empty($fileInfo['giangVienBienSoan']) && preg_match('/^Tác giả biên soạn NHCHT và đáp án:\s*(.+)$/i', $text, $matches)) {
                    $fileInfo['giangVienBienSoan'] = trim($matches[1]);
                    Log::info('Đã tìm thấy tên giảng viên biên soạn: ' . $fileInfo['giangVienBienSoan']);
                }
                if (preg_match('/^([IVX]+)\.\s+Chương\/Chủ đề\s+(.+)$/i', $text, $matches)) {
                    $tenChuong = trim($matches[2]);
                    Log::info('Đang tìm chương: ' . $tenChuong);
                    $chuong = $ctDangKy->hocPhan->chuongs->first(function($item) use ($tenChuong) {
                        return stripos(
                            $this->removeVietnameseAccents($item->ten),
                            $this->removeVietnameseAccents($tenChuong)
                        ) !== false;
                    });
                    if ($chuong) {
                        $fileInfo['chuongHienTai'] = $chuong;
                        Log::info('Đã tìm thấy chương trong CSDL: ' . $chuong->ten . ' (ID: ' . $chuong->id . ')');
                    } else {
                        $errorMessages[] = "Không tìm thấy chương '$tenChuong' trong CSDL.";
                    }
                    continue;
                }
                if ($fileInfo['chuongHienTai'] && preg_match('/^Chuẩn đầu ra\s*:?\s*(.+?)[,|:]/i', $text, $matches)) {
                    $tenCDR = trim($matches[1]);
                    Log::info('Đang tìm chuẩn đầu ra: ' . $tenCDR);
                    $cdr = $ctDangKy->hocPhan->chuanDauRas->first(function($item) use ($tenCDR) {
                        return stripos(
                            $this->removeVietnameseAccents($item->ten),
                            $this->removeVietnameseAccents($tenCDR)
                        ) !== false;
                    });
                    if ($cdr) {
                        $fileInfo['chuanDauRaHienTai'] = $cdr;
                        Log::info('Đã tìm thấy CDR trong CSDL: ' . $cdr->ten . ' (ID: ' . $cdr->id . ')');
                    } else {
                        $errorMessages[] = "Không tìm thấy chuẩn đầu ra '$tenCDR' trong CSDL.";
                    }
                    continue;
                }
                if (preg_match('/^Câu hỏi:\s*(.+)/i', $text, $matches)) {
                    if ($currentQuestion && !empty($currentQuestion['cau_hoi'])) {
                        $questions[] = $currentQuestion;
                    }
                    $currentQuestion = [
                        'cau_hoi' => trim($matches[1]),
                        'dap_ans' => [],
                        'diem' => 0,
                        'muc_do' => 2,
                        'id_chuong' => $fileInfo['chuongHienTai'] ? $fileInfo['chuongHienTai']->id : null,
                        'id_chuan_dau_ra' => $fileInfo['chuanDauRaHienTai'] ? $fileInfo['chuanDauRaHienTai']->id : null
                    ];
                    Log::info('Khởi tạo câu hỏi mới', $currentQuestion);
                    continue;
                }
                if ($fileInfo['isTracNghiem'] && preg_match('/^([A-D][.)]\s*)(.*)/i', $text, $matches)) {
                    if ($currentQuestion) {
                        $currentQuestion['dap_ans'][] = [
                            'noi_dung' => trim($matches[2]),
                            'trang_thai' => false,
                            'diem' => 0
                        ];
                        Log::info('Thêm đáp án trắc nghiệm', ['noi_dung' => trim($matches[2])]);
                    }
                    continue;
                }
                if (!$fileInfo['isTracNghiem'] && preg_match('/^Nội dung ý\s+(\d+|\.+):\s*(.+)/i', $text, $matches)) {
                    if ($currentQuestion) {
                        $currentQuestion['dap_ans'][] = [
                            'noi_dung' => trim($matches[2]),
                            'trang_thai' => true,
                            'diem' => 0
                        ];
                        Log::info('Thêm đáp án tự luận', ['noi_dung' => trim($matches[2])]);
                    }
                    continue;
                }
                if (preg_match('/^([\d.,]+)d$/i', $text, $matches) || preg_match('/^Điểm:\s*([\d.,]+)/i', $text, $matches)) {
                    $diem = (float) str_replace(',', '.', $matches[1]);
                    if ($currentQuestion && !empty($currentQuestion['dap_ans'])) {
                        $lastIndex = count($currentQuestion['dap_ans']) - 1;
                        $currentQuestion['dap_ans'][$lastIndex]['diem'] = $diem;
                        if ($diem > 0) {
                            $currentQuestion['dap_ans'][$lastIndex]['trang_thai'] = true;
                        }
                        Log::info('Cập nhật điểm đáp án', ['index' => $lastIndex, 'diem' => $diem]);
                    } elseif ($currentQuestion) {
                        $currentQuestion['diem'] = $diem;
                        Log::info('Cập nhật điểm câu hỏi', ['diem' => $diem]);
                    }
                    continue;
                }
                if (preg_match('/^Mức độ:\s*(Dễ|Trung bình|Khó)/i', $text, $matches)) {
                    if ($currentQuestion) {
                        $mucDoText = strtolower($matches[1]);
                        switch ($mucDoText) {
                            case 'dễ': $currentQuestion['muc_do'] = 1; break;
                            case 'trung bình': $currentQuestion['muc_do'] = 2; break;
                            case 'khó': $currentQuestion['muc_do'] = 3; break;
                        }
                        Log::info('Cập nhật mức độ câu hỏi', ['muc_do' => $currentQuestion['muc_do']]);
                    }
                    continue;
                }
            }
            if ($currentQuestion && !empty($currentQuestion['cau_hoi'])) {
                $questions[] = $currentQuestion;
            }
            Log::info('Tổng số câu hỏi tìm thấy: ' . count($questions));

            if (!empty($fileInfo['tenHocPhan']) && $ctDangKy->hocPhan) {
                $hocPhanNameMatched = stripos(
                    $this->removeVietnameseAccents($fileInfo['tenHocPhan']),
                    $this->removeVietnameseAccents($ctDangKy->hocPhan->ten)
                ) !== false;
                if (!$hocPhanNameMatched) {
                    $errorMessages[] = "Tên học phần trong file ('{$fileInfo['tenHocPhan']}') không khớp với học phần đã chọn ('{$ctDangKy->hocPhan->ten}')";
                }
            }
            if (!empty($fileInfo['giangVienBienSoan'])) {
                $gvNames = $ctDangKy->dsGvBienSoans->map(function($gv) {
                    return $gv->vienChuc ? $gv->vienChuc->name : '';
                })->filter()->join(', ');
                $gvNameMatched = false;
                foreach (explode(',', $fileInfo['giangVienBienSoan']) as $gvName) {
                    $gvName = trim($gvName);
                    if (!empty($gvName) && stripos($this->removeVietnameseAccents($gvNames), $this->removeVietnameseAccents($gvName)) !== false) {
                        $gvNameMatched = true;
                        break;
                    }
                }
                if (!$gvNameMatched && !empty($gvNames)) {
                    $errorMessages[] = "Giảng viên biên soạn trong file ('{$fileInfo['giangVienBienSoan']}') không khớp với giảng viên đã đăng ký ('{$gvNames}')";
                }
            }

            // Kiểm tra lỗi trước khi lưu DB
            if (count($errorMessages) > 0) {
                DB::rollBack();
                Log::warning('Import bị hủy do phát hiện lỗi dữ liệu.');
                return redirect()->route('cauhoi.danhsach', $request->id_ct_ds_dang_ky)
                    ->with('error', 'Không thể import câu hỏi. Vui lòng kiểm tra lại lỗi bên dưới.')
                    ->with('errorMessages', $errorMessages);
            }
            // Nếu không có lỗi, mới bắt đầu lưu vào DB
            $successCount = 0;
            $isTracNghiem = $fileInfo['isTracNghiem'] ?? ($ctDangKy->hinh_thuc_thi === 'Trắc nghiệm' || $ctDangKy->hinh_thuc_thi === 0);
            foreach ($questions as $index => $questionData) {
                Log::info("Đang xử lý câu hỏi #{$index}", $questionData);
                if (empty($questionData['cau_hoi'])) {
                    Log::warning("Bỏ qua câu hỏi #{$index} do không có nội dung");
                    continue;
                }
                if (isset($questionData['dap_ans']) && !empty($questionData['dap_ans'])) {
                    $tongDiemDapAn = 0;
                    foreach ($questionData['dap_ans'] as $dapAn) {
                        $tongDiemDapAn += (float)($dapAn['diem'] ?? 0);
                    }
                    $tongDiemDapAn = round($tongDiemDapAn, 2);
                    $tongDiem = round((float)($questionData['diem'] ?? 0), 2);
                    if ($tongDiemDapAn != $tongDiem) {
                        $errorMessages[] = "Câu hỏi #{$index} có tổng điểm đáp án ({$tongDiemDapAn}) không khớp với điểm câu hỏi ({$tongDiem})";
                    }
                }
                try {
                    $cauHoi = new CauHoi([
                        'cau_hoi' => $questionData['cau_hoi'],
                        'id_ct_ds_dang_ky' => $request->id_ct_ds_dang_ky,
                        'phan_loai' => $isTracNghiem ? 0 : 1,
                        'id_chuan_dau_ra' => $questionData['id_chuan_dau_ra'] ?? ($fileInfo['chuanDauRaHienTai'] ? $fileInfo['chuanDauRaHienTai']->id : null),
                        'id_chuong' => $questionData['id_chuong'] ?? ($fileInfo['chuongHienTai'] ? $fileInfo['chuongHienTai']->id : null),
                        'diem' => $questionData['diem'] ?? 0.5,
                        'muc_do' => $questionData['muc_do'] ?? 2,
                    ]);
                    if ($cauHoi->id_chuan_dau_ra === null || $cauHoi->id_chuong === null) {
                        $errorMessages[] = "Bỏ qua câu hỏi #{$index} do thiếu thông tin chương hoặc CDR";
                        continue;
                    }
                    $cauHoi->save();
                    $successCount++;
                    Log::info("Đã lưu câu hỏi #{$index} với ID: " . $cauHoi->id);
                    if ($isTracNghiem && isset($questionData['dap_ans']) && !empty($questionData['dap_ans'])) {
                        foreach ($questionData['dap_ans'] as $dapAn) {
                            $dapAnDb = $cauHoi->dapAns()->create([
                                'dap_an' => $dapAn['noi_dung'],
                                'trang_thai' => $dapAn['trang_thai'] ?? false,
                                'diem' => $dapAn['diem'] ?? 0
                            ]);
                            Log::info("Đã lưu đáp án trắc nghiệm với ID: " . $dapAnDb->id);
                        }
                    } elseif (!$isTracNghiem && isset($questionData['dap_ans']) && !empty($questionData['dap_ans'])) {
                        foreach ($questionData['dap_ans'] as $dapAnIndex => $dapAn) {
                            $diem = (float)($dapAn['diem'] ?? 0);
                            if ($diem < 0.25 || $diem > 0.5) {
                                $errorMessages[] = "Đáp án tự luận #{$dapAnIndex} của câu hỏi #{$index} có điểm không hợp lệ: {$diem} (chỉ chấp nhận từ 0.25 đến 0.5)";
                                continue;
                            }
                            $dapAnDb = $cauHoi->dapAns()->create([
                                'dap_an' => $dapAn['noi_dung'],
                                'trang_thai' => true,
                                'diem' => $diem
                            ]);
                            Log::info("Đã lưu đáp án tự luận với ID: " . $dapAnDb->id);
                        }
                    } else {
                        $dapAnDb = $cauHoi->dapAns()->create([
                            'dap_an' => $questionData['dap_an'] ?? 'Đáp án mẫu',
                            'trang_thai' => true,
                            'diem' => $cauHoi->diem
                        ]);
                        Log::info("Đã lưu đáp án mẫu với ID: " . $dapAnDb->id);
                    }
                } catch (\Exception $e) {
                    $errorMessages[] = "Lỗi khi lưu câu hỏi #{$index}: " . $e->getMessage();
                }
            }
            
            DB::commit();
            Log::info("Import thành công {$successCount} câu hỏi");
            return redirect()->route('cauhoi.danhsach', $request->id_ct_ds_dang_ky)
                ->with('success', "Đã import thành công {$successCount} câu hỏi.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi import câu hỏi: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->route('cauhoi.danhsach', $request->id_ct_ds_dang_ky)
                ->with('error', 'Lỗi khi import câu hỏi: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết câu hỏi
     */
    public function chiTiet($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->pluck('name');
        $role = 'user';
        
        if($roles->contains('Giảng viên')){
            $role = 'gv';
        }
        elseif($roles->contains('Trưởng Bộ Môn')){
            $role = 'tbm';
        }
        elseif($roles->contains('Nhân viên P.ĐBCL')){
            $role = 'dbcl';
        }
        elseif($roles->contains('Admin')){
            $role = 'admin';
        }
        
        $cauHoi = CauHoi::with(['dapAns', 'chuong', 'chuanDauRa', 'ctDSDangKy.hocPhan'])->findOrFail($id);
        
        return Inertia::render('CauHoi/ChiTiet', [
            'cauHoi' => $cauHoi,
            'role' => $role
        ]);
    }

    /**
     * Hiển thị form sửa câu hỏi
     */
    public function sua($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->pluck('name');
        $role = 'user';
        
        if($roles->contains('Giảng viên')){
            $role = 'gv';
        }
        elseif($roles->contains('Trưởng Bộ Môn')){
            $role = 'tbm';
        }
        elseif($roles->contains('Nhân viên P.ĐBCL')){
            $role = 'dbcl';
        }
        elseif($roles->contains('Admin')){
            $role = 'admin';
        }
        
        $cauHoi = CauHoi::with(['dapAns', 'ctDSDangKy.hocPhan'])->findOrFail($id);
        
        // Lấy danh sách chuẩn đầu ra và chương của học phần
        $chuanDauRas = \App\Models\ChuanDauRa::where('id_hoc_phan', $cauHoi->ctDSDangKy->hocPhan->id)
            ->where('able', true)
            ->get();
            
        $chuongs = \App\Models\Chuong::where('id_hoc_phan', $cauHoi->ctDSDangKy->hocPhan->id)
            ->where('able', true)
            ->get();
        
        return Inertia::render('CauHoi/Sua', [
            'cauHoi' => $cauHoi,
            'chuanDauRas' => $chuanDauRas,
            'chuongs' => $chuongs,
            'role' => $role
        ]);
    }

    /**
     * Cập nhật câu hỏi
     */
    public function capNhat(Request $request, $id)
    {
        $validated = $request->validate([
            'cau_hoi' => 'required|string',
            'id_chuan_dau_ra' => 'required|exists:chuan_dau_ras,id',
            'id_chuong' => 'required|exists:chuongs,id',
            'diem' => 'required|numeric|min:0',
            'muc_do' => 'required|in:1,2,3', // 1: dễ, 2: trung bình, 3: khó
            'dap_ans' => 'required|array', // Bắt buộc cho cả trắc nghiệm và tự luận
            'dap_ans.*.noi_dung' => 'required|string',
            'dap_ans.*.trang_thai' => 'required_if:phan_loai,0|boolean', // Chỉ bắt buộc cho trắc nghiệm
            'dap_ans.*.diem' => 'required|numeric|min:0.25|max:0.5',
            'dap_ans.*.id' => 'nullable|exists:dap_ans,id', // ID đáp án (nếu đã tồn tại)
        ]);

        $cauHoi = CauHoi::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Tính tổng điểm từ các đáp án
            $tongDiem = 0;
            foreach ($request->dap_ans as $dapAn) {
                if ($cauHoi->phan_loai == 0) {
                    // Nếu là trắc nghiệm, chỉ tính điểm của đáp án đúng
                    if (isset($dapAn['trang_thai']) && $dapAn['trang_thai']) {
                        $tongDiem += $dapAn['diem'];
                    }
                } else {
                    // Nếu là tự luận, tính tổng điểm của tất cả đáp án
                    $tongDiem += $dapAn['diem'];
                }
            }
            
            // Cập nhật thông tin câu hỏi
            $cauHoi->update([
                'cau_hoi' => $validated['cau_hoi'],
                'id_chuan_dau_ra' => $validated['id_chuan_dau_ra'],
                'id_chuong' => $validated['id_chuong'],
                'diem' => $tongDiem,
                'muc_do' => $validated['muc_do']
            ]);

            // Xử lý đáp án
            $dapAnIds = [];
            foreach ($request->dap_ans as $dapAnData) {
                if (isset($dapAnData['id'])) {
                    // Cập nhật đáp án đã tồn tại
                    $dapAn = \App\Models\DapAn::find($dapAnData['id']);
                    if ($dapAn) {
                        $dapAn->update([
                            'dap_an' => $dapAnData['noi_dung'],
                            'trang_thai' => $cauHoi->phan_loai == 0 ? $dapAnData['trang_thai'] : true,
                            'diem' => $dapAnData['diem']
                        ]);
                        $dapAnIds[] = $dapAn->id;
                    }
                } else {
                    // Tạo mới đáp án
                    $dapAn = $cauHoi->dapAns()->create([
                        'dap_an' => $dapAnData['noi_dung'],
                        'trang_thai' => $cauHoi->phan_loai == 0 ? $dapAnData['trang_thai'] : true,
                        'diem' => $dapAnData['diem']
                    ]);
                    $dapAnIds[] = $dapAn->id;
                }
            }
            
            // Xóa các đáp án không có trong request
            $cauHoi->dapAns()->whereNotIn('id', $dapAnIds)->delete();

            DB::commit();
            return redirect()->route('cauhoi.danhsach', $cauHoi->id_ct_ds_dang_ky)
                ->with('success', 'Cập nhật câu hỏi thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật câu hỏi: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật câu hỏi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xóa câu hỏi
     */
    public function xoa($id)
    {
        $cauHoi = CauHoi::findOrFail($id);

        // Xóa tất cả đáp án trước để tránh lỗi khóa ngoại
        $cauHoi->dapAns()->delete();

        // Sau đó xóa câu hỏi
        $cauHoi->delete();

        return redirect()->route('cauhoi.danhsach', $cauHoi->id_ct_ds_dang_ky)
            ->with('success', 'Xóa câu hỏi thành công');
    }

    public function downloadMauImport($type)
    {
        $filePath = 'mau_import/';
        
        if ($type === 'trac_nghiem') {
            $filePath .= 'mau_import_trac_nghiem.docx';
            $fileName = 'Mẫu import câu hỏi trắc nghiệm.docx';
        } else {
            $filePath .= 'mau_import_tu_luan.docx';
            $fileName = 'Mẫu import câu hỏi tự luận vấn đáp.docx';
        }
        
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            return response()->download($fullPath, $fileName);
        } else {
            return back()->with('error', 'File mẫu không tồn tại.');
        }
    }

    /**
     * Tạo và tải xuống file Word tùy chỉnh theo mẫu với dữ liệu từ database
     */
    public function downloadTemplate($id)
    {
        // Lấy thông tin từ database
        $ctDSDangKy = CTDSDangKy::with([
            'hocPhan', 
            'hocPhan.chuongs', 
            'hocPhan.chuanDauRas', 
            'hocPhan.boMon', 
            'hocPhan.boMon.khoa', 
            'dsGvBienSoans.vienChuc',
            'dsDangKy'
        ])->findOrFail($id);
        
        // Lấy danh sách chuẩn đầu ra và chương
        $chuanDauRas = $ctDSDangKy->hocPhan->chuanDauRas;
        $chuongs = $ctDSDangKy->hocPhan->chuongs;
        
        // Lấy thông tin bộ môn, khoa
        $boMon = $ctDSDangKy->hocPhan->boMon;
        $khoa = $boMon ? $boMon->khoa : null;
        
        // Lấy thông tin giảng viên biên soạn
        $dsGVBienSoans = $ctDSDangKy->dsGvBienSoans;
        $gvBienSoanNames = $dsGVBienSoans->map(function($gv) {
            return $gv->vienChuc ? $gv->vienChuc->name : 'Không xác định';
        })->join(', ');

        // Xác định loại mẫu - trắc nghiệm hay tự luận
        $isTracNghiem = ($ctDSDangKy->hinh_thuc_thi === 'Trắc nghiệm' || $ctDSDangKy->hinh_thuc_thi == 0);
        
        // Khởi tạo PhpWord
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);
        
        // Thêm section
        $section = $phpWord->addSection();
        
        // Style cho bảng
        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 100,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER
        ];
        
        // Style cho cell tiêu đề
        $headerCellStyle = [
            'valign' => 'center',
            'bgColor' => 'FFFFFF',
            'borderSize' => 6,
            'borderColor' => '000000'
        ];
        
        // Style cho nội dung cell
        $cellStyle = [
            'valign' => 'top',
            'borderSize' => 6,
            'borderColor' => '000000'
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
        
        // Thêm thông tin trường, khoa, bộ môn
        $headerTableStyle = [
            'borderSize' => 0,
            'cellMargin' => 100
        ];
        
        $headerTable = $section->addTable($headerTableStyle);
        $headerTable->addRow();
        $headerTable->addCell(4000, ['borderSize' => 0])->addText('TRƯỜNG ĐẠI HỌC NHA TRANG', $boldTextStyle);
        $headerTable->addRow();
        $khoaText = $khoa ? 'KHOA/VIỆN: ' . $khoa->ten : 'KHOA/VIỆN: .............................';
        $headerTable->addCell(4000, ['borderSize' => 0])->addText($khoaText, $boldTextStyle);
        $headerTable->addRow();
        $boMonText = $boMon ? 'BỘ MÔN: ' . $boMon->ten : 'BỘ MÔN: ..................................';
        $headerTable->addCell(4000, ['borderSize' => 0])->addText($boMonText, $boldTextStyle);
        
        // Thêm tiêu đề
        $section->addTextBreak(1);
        
        // Sử dụng fontStyle với alignment được định nghĩa rõ ràng
        $fontStyle = new \PhpOffice\PhpWord\Style\Font();
        $fontStyle->setBold(true);
        $fontStyle->setName('Times New Roman');
        $fontStyle->setSize(14);
        
        // Tạo paragraph với alignment là center
        $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
        $paragraphStyle->setAlignment(\PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $paragraphStyle->setSpaceAfter(0);
        
        // Áp dụng cả paragraph style và font style
        $section->addText('BẢNG NGÂN HÀNG CÂU HỎI THI, ĐÁP ÁN VÀ THANG ĐIỂM', $fontStyle, $paragraphStyle);
        
        // Tương tự cho phụ đề
        $fontStyleItalic = new \PhpOffice\PhpWord\Style\Font();
        $fontStyleItalic->setItalic(true);
        $fontStyleItalic->setName('Times New Roman');
        $fontStyleItalic->setSize(13);
        
        $paragraphStyleSubtitle = new \PhpOffice\PhpWord\Style\Paragraph();
        $paragraphStyleSubtitle->setAlignment(\PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $paragraphStyleSubtitle->setSpaceAfter(100);
        
        // Thêm phụ đề tùy thuộc vào loại mẫu
        if ($isTracNghiem) {
            $section->addText('(Dùng cho câu hỏi thi trắc nghiệm)', $fontStyleItalic, $paragraphStyleSubtitle);
        } else {
            $section->addText('(Dùng cho câu hỏi thi tự luận, vấn đáp)', $fontStyleItalic, $paragraphStyleSubtitle);
        }
        $section->addTextBreak(1);
        
        // Thêm thông tin HP
        $hocPhanText = 'Tên HP: ' . ($ctDSDangKy->hocPhan ? $ctDSDangKy->hocPhan->ten : '......................................');
        $section->addText($hocPhanText);
        $section->addText('Tác giả biên soạn NHCHT và đáp án: ' . $gvBienSoanNames);
        $section->addTextBreak(1);
       
        // Tạo bảng chính
        $table = $section->addTable($tableStyle);
        
        // Style cho chữ căn giữa cả chiều ngang và dọc
        $centeredTextStyle = [
            'bold' => true,
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
            
        ];
        
        // Hàng tiêu đề
        $table->addRow();
        $table->addCell(1000, [
            'valign' => 'center', 
            'bgColor' => 'FFFFFF',
            'borderSize' => 6,
            'borderColor' => '000000'
        ])->addText('Câu hỏi', $centeredTextStyle);
        
        $table->addCell(4000, [
            'valign' => 'center', 
            'bgColor' => 'FFFFFF',
            'borderSize' => 6,
            'borderColor' => '000000'
        ])->addText('Nội dung', $centeredTextStyle);
        
        if ($isTracNghiem) {
            $table->addCell(1500, [
                'valign' => 'center', 
                'bgColor' => 'FFFFFF',
                'borderSize' => 6,
                'borderColor' => '000000'
            ])->addText('Điểm', $centeredTextStyle);
        } else {
            $table->addCell(1500, [
                'valign' => 'center', 
                'bgColor' => 'FFFFFF',
                'borderSize' => 6,
                'borderColor' => '000000'
            ])->addText('Điểm (Mỗi ý từ 0,25 -0,5 đ)', $centeredTextStyle);
        }
        
        $table->addCell(1500, [
            'valign' => 'center', 
            'bgColor' => 'FFFFFF',
            'borderSize' => 6,
            'borderColor' => '000000'
        ])->addText('Độ khó (Dễ, Trung bình, Khó)', $centeredTextStyle);
        
        // Thêm các chương và chuẩn đầu ra
        foreach ($chuongs as $index => $chuong) {
            // Sử dụng số La Mã
            $lamaNumbers = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
            $chapterNumber = isset($lamaNumbers[$index]) ? $lamaNumbers[$index] : ($index + 1);
            
            // Tiêu đề chương
            $table->addRow();
            $cell = $table->addCell(8000, ['gridSpan' => 4]);
            $cell->addText("$chapterNumber. Chương/Chủ đề " . $chuong->ten, $boldTextStyle);
            
            // Lọc chuẩn đầu ra theo bảng quan hệ chuong_chuan_dau_ra (nếu có)
            $chuongCDRs = $chuanDauRas; // Mặc định lấy tất cả chuẩn đầu ra
            
            // Kiểm tra xem chương có mối quan hệ với chuẩn đầu ra không
            $relations = $chuong->chuongChuanDauRa;
            if ($relations && $relations->count() > 0) {
                // Lấy danh sách ID của các chuẩn đầu ra liên quan đến chương này
                $cdrIds = $relations->pluck('id_chuan_dau_ra');
                
                // Lọc chuẩn đầu ra theo các ID đã thu thập
                $chuongCDRs = $chuanDauRas->whereIn('id', $cdrIds);
            }
            
            foreach ($chuongCDRs as $cdrIndex => $cdr) {
                // Tiêu đề chuẩn đầu ra
                $table->addRow();
                $cell = $table->addCell(8000, ['gridSpan' => 4]);
                $cdrText = "Chuẩn đầu ra " . $cdr->ten . ", Số lượng câu hỏi: ...................................";
                $cell->addText($cdrText);
                
                // Tạo mẫu câu hỏi (2 câu hỏi cho mỗi CDR)
                for ($qIndex = 1; $qIndex <= 2; $qIndex++) {
                    // Câu hỏi
                    $table->addRow();
                    
                    // Tạo cell với căn giữa cả chiều ngang và dọc cho số câu hỏi
                    $cell = $table->addCell(1000, [
                        'vMerge' => 'restart', 
                        'valign' => 'center',
                        'borderSize' => 6,
                        'borderColor' => '000000'
                    ]);
                    
                    // Thêm văn bản với căn giữa
                    $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
                    $paragraphStyle->setAlignment(\PhpOffice\PhpWord\SimpleType\Jc::CENTER);
                    
                    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
                    $fontStyle->setBold(true);
                    
                    $cell->addText($qIndex, $fontStyle, $paragraphStyle);
                    
                    $table->addCell(4000, $cellStyle)->addText('Câu hỏi: .........................................');
                    $table->addCell(1500, $cellStyle)->addText('..........d');
                    $table->addCell(1500, $cellStyle)->addText('.........');
                    
                    // Đáp án
                    $table->addRow();
                    $table->addCell(1000, ['vMerge' => 'continue']);
                    $table->addCell(4000, $cellStyle)->addText('Đáp án:');
                    $table->addCell(1500, $cellStyle);
                    $table->addCell(1500, $cellStyle);
                    
                    if ($isTracNghiem) {
                        // Các lựa chọn trắc nghiệm
                        $options = ['A', 'B', 'C', 'D'];
                        foreach ($options as $option) {
                            $table->addRow();
                            $table->addCell(1000, ['vMerge' => 'continue']);
                            $table->addCell(4000, $cellStyle)->addText("$option. .......................................");
                            $table->addCell(1500, $cellStyle)->addText('..........d');
                            $table->addCell(1500, $cellStyle);
                        }
                    } else {
                        // Nội dung các ý cho tự luận
                        for ($i = 1; $i <= 3; $i++) {
                            $text = ($i < 3) ? "Nội dung ý $i: ........................." : "Nội dung ý ... .........................";
                            $table->addRow();
                            $table->addCell(1000, ['vMerge' => 'continue']);
                            $table->addCell(4000, $cellStyle)->addText($text);
                            $table->addCell(1500, $cellStyle)->addText('..........d');
                            $table->addCell(1500, $cellStyle);
                        }
                    }
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
        $rightStyle = [
            'bold' => true,
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END
        ];
        $rightItalicStyle = [
            'italic' => true,
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END
        ];
        $section->addText('TRƯỞNG BM/KHOA/VIỆN', $rightStyle);
        $section->addText('(Ký và ghi rõ họ tên)', $rightItalicStyle);
        
        // Lấy thông tin học kỳ và năm học
        $hocKy = '';
        $namHoc = '';
        if ($ctDSDangKy->dsDangKy) {
            $hocKy = $ctDSDangKy->dsDangKy->hoc_ki ?? '';
            $namHoc = $ctDSDangKy->dsDangKy->nam_hoc ?? '';
        }
        
        // Định dạng loại file
        $phanLoai = $isTracNghiem ? 'trac_nghiem' : 'tu_luan';
        
        // Lưu file
        $tenHocPhan = '';
        
        // Đảm bảo có tên học phần trong tên file
        if ($ctDSDangKy->hocPhan && !empty($ctDSDangKy->hocPhan->ten)) {
            // Loại bỏ dấu tiếng Việt
            $tenKhongDau = $this->removeVietnameseAccents($ctDSDangKy->hocPhan->ten);
            $tenHocPhan = preg_replace('/[^a-zA-Z0-9]/', '_', $tenKhongDau);
        } elseif ($ctDSDangKy->hocPhan && !empty($ctDSDangKy->hocPhan->id)) {
            // Nếu không có tên học phần, dùng mã học phần (đã loại bỏ dấu)
            $maKhongDau = $this->removeVietnameseAccents($ctDSDangKy->hocPhan->id);
            $tenHocPhan = preg_replace('/[^a-zA-Z0-9]/', '_', $maKhongDau);
        } else {
            // Nếu không có cả tên và mã học phần, dùng 'Nhp' (Ngân hàng câu hỏi)
            $tenHocPhan = 'Nhp';
        }
        
        $fileName = $tenHocPhan;
        
        // Thêm học kỳ và năm học nếu có
        if (!empty($hocKy)) {
            $fileName .= '_hk' . $hocKy;
        }
        
        if (!empty($namHoc)) {
            $fileName .= '_' . $namHoc;
        }
        
        // Thêm phân loại
        $fileName .= '_' . $phanLoai . '.docx';
        
        $tempFilePath = storage_path('app/temp/' . $fileName);
        
        // Đảm bảo thư mục tồn tại
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFilePath);
        
        return response()->download($tempFilePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Chuyển đổi chuỗi tiếng Việt có dấu thành không dấu
     */
    private function removeVietnameseAccents($str) {
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd'=>'đ',
            'D'=>'Đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
        );
        
        foreach($unicode as $nonUnicode=>$uni){
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        
        return $str;
    }
}