<?php

namespace App\Http\Controllers\Giangvien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use App\Models\CauHoi;
use App\Models\DapAn;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ImportCauHoiController extends Controller
{
    public function index()
    {
        $khoas = \App\Models\Khoa::where('able', true)
            ->whereNotIn('id', ['admin', 'DBCL'])
            ->get();
        $bo_mons = \App\Models\BoMon::where('able', true)->get();
        $hoc_phans = \App\Models\HocPhan::where('able', true)->get();

        // Lấy danh sách CTDSDangKy mà giảng viên tham gia biên soạn
        $ct_ds_dang_kies = \App\Models\CTDSDangKy::whereHas('dsGVBienSoans', function($query) {
                $query->where('id_vien_chuc', Auth::id());
            })
            ->where('able', true)
            ->with(['hocPhan', 'dsDangKy'])
            ->get()
            ->map(function($ct) {
                return [
                    'id' => $ct->id,
                    'ten' => $ct->dsDangKy->nam_hoc . ' - ' . 
                            $ct->dsDangKy->hoc_ki . ' - ' . 
                            $ct->hocPhan->ten . ' (' . $ct->hocPhan->id . ')'
                ];
            });

        return Inertia::render('Giangvien/CauHoi/Import', [
            'khoas' => $khoas,
            'bo_mons' => $bo_mons,
            'hoc_phans' => $hoc_phans,
            'ct_ds_dang_kies' => $ct_ds_dang_kies
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:docx|max:2048',
            'loai' => 'required|in:tu_luan,trac_nghiem',
            'id_ct_ds_dang_ky' => 'required|exists:c_t_d_s_dang_kies,id'
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $phpWord = IOFactory::load($file->getPathname());

            $questions = [];
            $currentQuestion = null;
            
            // Đọc từng đoạn văn trong file
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                        $text = '';
                        foreach ($element->getElements() as $textElement) {
                            if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                                $text .= $textElement->getText();
                            }
                        }
                        
                        // Xử lý text dựa vào loại câu hỏi
                        if ($request->loai === 'tu_luan') {
                            $this->processTextForTuLuan($text, $questions, $currentQuestion);
                        } else {
                            $this->processTextForTracNghiem($text, $questions, $currentQuestion);
                        }
                    }
                }
            }

            // Lưu câu hỏi và đáp án vào database
            foreach ($questions as $question) {
                $cauHoi = CauHoi::create([
                    'noi_dung' => $question['noi_dung'],
                    'diem' => $question['diem'] ?? 0,
                    'muc_do' => $question['muc_do'] ?? 'trung_binh',
                    'loai' => $request->loai,
                    'id_ct_ds_dang_ky' => $request->id_ct_ds_dang_ky,
                    'id_chuong' => $question['id_chuong'] ?? null,
                    'id_chuan_dau_ra' => $question['id_chuan_dau_ra'] ?? null
                ]);

                // Lưu đáp án
                if (isset($question['dap_an'])) {
                    foreach ($question['dap_an'] as $dapAn) {
                        DapAn::create([
                            'noi_dung' => $dapAn['noi_dung'],
                            'trang_thai' => $dapAn['trang_thai'] ?? false,
                            'id_cau_hoi' => $cauHoi->id
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Import câu hỏi thành công');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    private function processTextForTuLuan($text, &$questions, &$currentQuestion)
    {
        $text = trim($text);
        
        // Bắt đầu câu hỏi mới
        if (preg_match('/^Câu hỏi:/', $text)) {
            if ($currentQuestion) {
                $questions[] = $currentQuestion;
            }
            $currentQuestion = [
                'noi_dung' => trim(str_replace('Câu hỏi:', '', $text)),
                'dap_an' => []
            ];
        }
        // Xử lý điểm
        elseif (preg_match('/^\.+d$/', $text)) {
            if ($currentQuestion) {
                $currentQuestion['diem'] = floatval(str_replace('d', '', $text));
            }
        }
        // Xử lý đáp án
        elseif (preg_match('/^Nội dung ý \d+:/', $text) && $currentQuestion) {
            $currentQuestion['dap_an'][] = [
                'noi_dung' => trim(str_replace('Nội dung ý:', '', $text)),
                'trang_thai' => false
            ];
        }
    }

    private function processTextForTracNghiem($text, &$questions, &$currentQuestion)
    {
        $text = trim($text);
        
        // Bắt đầu câu hỏi mới
        if (preg_match('/^Câu hỏi:/', $text)) {
            if ($currentQuestion) {
                $questions[] = $currentQuestion;
            }
            $currentQuestion = [
                'noi_dung' => trim(str_replace('Câu hỏi:', '', $text)),
                'dap_an' => []
            ];
        }
        // Xử lý điểm
        elseif (preg_match('/^\.+d$/', $text)) {
            if ($currentQuestion) {
                $currentQuestion['diem'] = floatval(str_replace('d', '', $text));
            }
        }
        // Xử lý đáp án
        elseif (preg_match('/^[A-D]\./', $text) && $currentQuestion) {
            $dapAn = trim(substr($text, 2));
            $currentQuestion['dap_an'][] = [
                'noi_dung' => $dapAn,
                'trang_thai' => false // Mặc định là sai, sẽ được cập nhật sau
            ];
        }
    }
} 