<?php

namespace App\Http\Controllers\TBM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chuong;
use App\Models\HocPhan;
use App\Models\MaTran;
use App\Models\ChuongChuanDauRa;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class MatranController extends Controller
{
    private function toRoman($num) {
        $n = intval($num);
        $res = '';
        $roman_numerals = array(
            'M'  => 1000,
            'CM' => 900,
            'D'  => 500,
            'CD' => 400,
            'C'  => 100,
            'XC' => 90,
            'L'  => 50,
            'XL' => 40,
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1
        );
        foreach ($roman_numerals as $roman => $number) {
            $matches = intval($n / $number);
            $res .= str_repeat($roman, $matches);
            $n = $n % $number;
        }
        return $res;
    }

    public function create(Request $request)
    {
        $dsHocPhan =  HocPhan::with('chuongs', 'chuanDauRas')->get();
        $chuongIdsDaCoMaTran =  MaTran::distinct()->pluck('id_chuong')->toArray();

        // Lấy danh sách id_hoc_phan của các chương này
        $hpDaCoMaTran =  Chuong::whereIn('id', $chuongIdsDaCoMaTran)
            ->distinct()
            ->pluck('id_hoc_phan')
            ->toArray();

        // Lọc ra các học phần chưa có ma trận
        $hocPhans = $dsHocPhan->filter(function($hp) use ($hpDaCoMaTran) {
            return !in_array($hp->id, $hpDaCoMaTran);
        })->values();
        // Nếu có request chọn học phần thì trả về thêm danh sách chương, CDR
        $chuongs = [];
        $cdrs = [];
        $giao = [];
        if ($request->filled('hoc_phan_id')) {
            $hocPhan = $dsHocPhan->where('id', $request->hoc_phan_id)->first();
            if ($hocPhan) {
                $chuongs = $hocPhan->chuongs;
                $cdrs = $hocPhan->chuanDauRas;
                // Lấy các cặp giao giữa chương và CDR (bảng chuong_chuan_dau_ra)
                $giao = [];
                foreach ($chuongs as $ch) {
                    foreach ($ch->chuongChuanDauRa as $pivot) {
                        $giao[] = [$ch->id, $pivot->id_chuan_dau_ra];
                    }
                }
                // Sau khi lấy $chuongs, $cdrs, $giao
                Log::info('chuongs', $chuongs->toArray());
                Log::info('cdrs', $cdrs->toArray());
                Log::info('giao', $giao);
            }
        }
        return Inertia::render('TBM/Matran/Create', [
            'hocPhans' => $hocPhans,
            'chuongs' => $chuongs,
            'cdrs' => $cdrs,
            'giao' => $giao,
            'selectedHocPhan' => $request->hoc_phan_id ?? null
        ]);
    }

    public function store(Request $request)
    {
        try {
            // Validate dữ liệu đầu vào
            $request->validate([
                'hoc_phan' => 'required|exists:hoc_phans,id',
                'bang' => 'required|array',
                'bang.*' => 'array',
                'bang.*.*' => 'array',
                'bang.*.*.*' => 'required|integer|min:0',
            ]);

            // Kiểm tra xem học phần đã có ma trận chưa
            $chuongIds = array_keys($request->input('bang'));
            // Lấy id_hoc_phan từ bảng chuongs
            $chuongs = Chuong::whereIn('id', $chuongIds)->get();
            $existingMatran = MaTran::whereIn('id_chuong', $chuongIds)
                ->whereIn('id_chuong', $chuongs->pluck('id'))
                ->exists();
            
            if ($existingMatran) {
                return back()->withErrors(['error' => 'Học phần này đã có ma trận!']);
            }

            // Chuyển đổi dữ liệu từ form sang format lưu DB
            $bang = $request->input('bang');
            $data = [];
            foreach ($bang as $chuongId => $cdrArr) {
                foreach ($cdrArr as $cdrId => $mucArr) {
                    // Kiểm tra xem cặp chương-CDR có tồn tại trong bảng chuong_chuan_dau_ra không
                    $exists = ChuongChuanDauRa::where('id_chuong', $chuongId)
                        ->where('id_chuan_dau_ra', $cdrId)
                        ->exists();
                    
                    if (!$exists) {
                        Log::warning("Cặp chương-CDR không tồn tại: chuong_id={$chuongId}, cdr_id={$cdrId}");
                        continue;
                    }

                    $data[] = [
                        'id_chuong' => $chuongId,
                        'id_chuan_dau_ra' => $cdrId,
                        'diem'=>0,
                        'so_cau_de' => $mucArr[1] ?? 0,
                        'so_cau_tb' => $mucArr[2] ?? 0,
                        'so_cau_kho' => $mucArr[3] ?? 0,
                        'able' => true
                    ];
                }
            }

            // Sử dụng transaction để đảm bảo tính toàn vẹn dữ liệu
            DB::beginTransaction();
            try {
                // Lưu từng dòng vào bảng ma_tran
                foreach ($data as $row) {
                    MaTran::create($row);
                }
                DB::commit();
                
                Log::info('Tạo ma trận thành công', [
                    'hoc_phan_id' => $request->hoc_phan,
                    'so_dong' => count($data)
                ]);
                
                return redirect()->route('tbm.matran.index')
                    ->with('success', 'Tạo ma trận thành công!');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Lỗi khi tạo ma trận: ' . $e->getMessage());
                return back()->withErrors(['error' => 'Có lỗi xảy ra khi tạo ma trận!']);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi validation hoặc xử lý dữ liệu: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Dữ liệu không hợp lệ!']);
        }
    }

    public function index(Request $request)
    {
        $allHocPhans = HocPhan::select('id', 'ten')->orderBy('ten')->get();

        $hocPhanIds = Chuong::whereHas('maTrans')->pluck('id_hoc_phan')->unique();
        $query = HocPhan::whereIn('id', $hocPhanIds)
            ->withCount(['chuongs'])
            ->orderBy('ten');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(ten) LIKE ?', ['%' . mb_strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(id) LIKE ?', ['%' . mb_strtolower($search) . '%']);
            });
        }

        $hocPhans = $query->get();

        return Inertia::render('TBM/Matran/Index', [
            'allHocPhans' => $allHocPhans,
            'hocPhans' => $hocPhans,
            'filters' => $request->only(['search'])
        ]);
    }

    public function show($id)
    {
        // Lấy học phần, chương, CDR, các cặp giao, và dữ liệu ma trận
        $hocPhan = HocPhan::with(['chuongs.chuanDauRas', 'chuanDauRas'])->findOrFail($id);
        $chuongs = $hocPhan->chuongs;
        $cdrs = $hocPhan->chuanDauRas;
        $giao = [];
        foreach ($chuongs as $ch) {
            foreach ($ch->chuongChuanDauRa as $pivot) {
                $giao[] = [$ch->id, $pivot->id_chuan_dau_ra];
            }
        }
        Log::info('GIAO_SHOW', $giao);
        $bang = [];
        foreach ($giao as [$chuongId, $cdrId]) {
            $row = MaTran::where('id_chuong', $chuongId)->where('id_chuan_dau_ra', $cdrId)->first();
            $bang[$chuongId][$cdrId] = [
                1 => $row ? $row->so_cau_de : 0,
                2 => $row ? $row->so_cau_tb : 0,
                3 => $row ? $row->so_cau_kho : 0,
            ];
        }
        Log::info('BANG_SHOW', $bang);
        return Inertia::render('TBM/Matran/Show', [
            'hocPhan' => $hocPhan,
            'chuongs' => $chuongs,
            'cdrs' => $cdrs,
            'giao' => $giao,
            'bang' => $bang,
            'id' => $id
        ]);
    }

    public function edit($id)
    {
        $hocPhan = HocPhan::with(['chuongs.chuanDauRas', 'chuanDauRas'])->findOrFail($id);
        $chuongs = $hocPhan->chuongs;
        $cdrs = $hocPhan->chuanDauRas;
        $giao = [];
        foreach ($chuongs as $ch) {
            foreach ($ch->chuongChuanDauRa as $pivot) {
                $giao[] = [$ch->id, $pivot->id_chuan_dau_ra];
            }
        }
        Log::info('GIAO_EDIT', $giao);
        $bang = [];
        foreach ($giao as [$chuongId, $cdrId]) {
            $row = MaTran::where('id_chuong', $chuongId)->where('id_chuan_dau_ra', $cdrId)->first();
            $bang[$chuongId][$cdrId] = [
                1 => $row ? $row->so_cau_de : 0,
                2 => $row ? $row->so_cau_tb : 0,
                3 => $row ? $row->so_cau_kho : 0,
            ];
        }
        Log::info('BANG_EDIT', $bang);
        return Inertia::render('TBM/Matran/Edit', [
            'hocPhan' => $hocPhan,
            'chuongs' => $chuongs,
            'cdrs' => $cdrs,
            'giao' => $giao,
            'bang' => $bang,
            'id' => $id
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'bang' => 'required|array',
            'bang.*' => 'array',
            'bang.*.*' => 'array',
            'bang.*.*.*' => 'integer|min:0',
        ]);

        $bang = $request->input('bang');

        // Cập nhật từng dòng ma trận
        foreach ($bang as $chuongId => $cdrArr) {
            foreach ($cdrArr as $cdrId => $mucArr) {
                $row = MaTran::where('id_chuong', $chuongId)
                    ->where('id_chuan_dau_ra', $cdrId)
                    ->first();
                if ($row) {
                    $row->update([
                        'so_cau_de' => $mucArr[1] ?? 0,
                        'so_cau_tb' => $mucArr[2] ?? 0,
                        'so_cau_kho' => $mucArr[3] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('tbm.matran.index')->with('success', 'Cập nhật ma trận thành công!');
    }

    public function export(Request $request, $id)
    {
        Log::info('EXPORT_REQUEST', [
            'id' => $id,
            'so_de' => $request->input('so_de'),
            'loai_de' => $request->input('loai_de')
        ]);
        $hocPhan = HocPhan::with(['chuongs.chuanDauRas', 'chuanDauRas', 'chuongs.cauHois'])->findOrFail($id);
        $chuongs = $hocPhan->chuongs;
        $cdrs = $hocPhan->chuanDauRas;
        $giao = [];
        foreach ($chuongs as $ch) {
            foreach ($ch->chuongChuanDauRa as $pivot) {
                $giao[] = [$ch->id, $pivot->id_chuan_dau_ra];
            }
        }
        $bang = [];
        foreach ($giao as [$chuongId, $cdrId]) {
            $row = MaTran::where('id_chuong', $chuongId)->where('id_chuan_dau_ra', $cdrId)->first();
            $bang[$chuongId][$cdrId] = [
                1 => $row ? $row->so_cau_de : 0,
                2 => $row ? $row->so_cau_tb : 0,
                3 => $row ? $row->so_cau_kho : 0,
            ];
        }
        $soDe = $request->input('so_de');
        $loaiDe = $request->input('loai_de');
        $dsDe = [];
        if ($soDe && is_numeric($soDe) && $soDe > 0) {
            for ($i = 1; $i <= $soDe; $i++) {
                $de = [];
                foreach ($bang as $chuongId => $cdrArr) {
                    foreach ($cdrArr as $cdrId => $mucArr) {
                        foreach ([1,2,3] as $muc) {
                            $soCau = $mucArr[$muc] ?? 0;
                            if ($soCau > 0) {
                                $cauHoiQuery = $chuongs->find($chuongId)?->cauHois()
                                    ->where('id_chuan_dau_ra', $cdrId)
                                    ->where('muc_do', $muc);
                                // Lọc theo loại đề
                                $phanLoaiLog = null;
                                if ($loaiDe === 'trac_nghiem') {
                                    $cauHoiQuery = $cauHoiQuery->where('phan_loai', 0);
                                    $phanLoaiLog = 0;
                                } elseif ($loaiDe === 'tu_luan_van_dap') {
                                    $cauHoiQuery = $cauHoiQuery->whereIn('phan_loai', [1,2]);
                                    $phanLoaiLog = [1,2];
                                } elseif ($loaiDe === 'tu_luan') {
                                    $cauHoiQuery = $cauHoiQuery->where('phan_loai', 1);
                                    $phanLoaiLog = 1;
                                } elseif ($loaiDe === 'van_dap') {
                                    $cauHoiQuery = $cauHoiQuery->where('phan_loai', 2);
                                    $phanLoaiLog = 2;
                                }
                                $cauHois = $cauHoiQuery->with('dapAns')->inRandomOrder()->limit($soCau)->get();
                                Log::info('EXPORT_CAU_HOI', [
                                    'de' => $i,
                                    'id_chuong' => $chuongId,
                                    'id_cdr' => $cdrId,
                                    'muc_do' => $muc,
                                    'phan_loai' => $phanLoaiLog,
                                    'so_cau_can' => $soCau,
                                    'so_cau_lay_duoc' => $cauHois->count(),
                                ]);
                                foreach ($cauHois as $cau) {
                                    $de[] = [
                                        'id' => $cau->id,
                                        'noi_dung' => $cau->cau_hoi,
                                        'id_chuong' => $cau->id_chuong,
                                        'id_chuan_dau_ra' => $cau->id_chuan_dau_ra,
                                        'muc_do' => $cau->muc_do,
                                        'phan_loai' => $cau->phan_loai,
                                        'dap_ans' => $cau->dapAns->map(function($da) {
                                            return [
                                                'id' => $da->id,
                                                'noi_dung' => $da->dap_an,
                                                'is_dap_an' => $da->trang_thai == 1
                                            ];
                                        })
                                    ];
                                }
                            }
                        }
                    }
                }
                Log::info('EXPORT_DE', ['de' => $i, 'so_cau' => count($de)]);
                $dsDe[] = $de;
            }
        }
        Log::info('EXPORT_RESULT', [
            'soDe' => $soDe,
            'loaiDe' => $loaiDe,
            'tong_de' => count($dsDe),
            'tong_cau_moi_de' => array_map('count', $dsDe)
        ]);
        return Inertia::render('TBM/Matran/Export', [
            'hocPhan' => $hocPhan,
            'chuongs' => $chuongs,
            'cdrs' => $cdrs,
            'giao' => $giao,
            'bang' => $bang,
            'id' => $id,
            'soDe' => $soDe,
            'dsDe' => $dsDe,
            'loaiDe' => $loaiDe
        ]);
    }

    public function exportDownload(Request $request, $id)
    {
        $deIndex = (int) $request->query('de', 1) - 1;
        $soDe = $request->input('so_de') ?? 1;
        $loaiDe = $request->input('loai_de') ?? '';

        // Lấy lại dữ liệu export như cũ
        $hocPhan = HocPhan::with(['chuongs.chuanDauRas', 'chuanDauRas', 'chuongs.cauHois'])->findOrFail($id);
        $chuongs = $hocPhan->chuongs;
        $cdrs = $hocPhan->chuanDauRas;
        $giao = [];
        foreach ($chuongs as $ch) {
            foreach ($ch->chuongChuanDauRa as $pivot) {
                $giao[] = [$ch->id, $pivot->id_chuan_dau_ra];
            }
        }
        $bang = [];
        foreach ($giao as [$chuongId, $cdrId]) {
            $row = MaTran::where('id_chuong', $chuongId)->where('id_chuan_dau_ra', $cdrId)->first();
            $bang[$chuongId][$cdrId] = [
                1 => $row ? $row->so_cau_de : 0,
                2 => $row ? $row->so_cau_tb : 0,
                3 => $row ? $row->so_cau_kho : 0,
            ];
        }
        $dsDe = [];
        for ($i = 1; $i <= $soDe; $i++) {
            $de = [];
            foreach ($bang as $chuongId => $cdrArr) {
                foreach ($cdrArr as $cdrId => $mucArr) {
                    foreach ([1,2,3] as $muc) {
                        $soCau = $mucArr[$muc] ?? 0;
                        if ($soCau > 0) {
                            $cauHoiQuery = $chuongs->find($chuongId)?->cauHois()
                                ->where('id_chuan_dau_ra', $cdrId)
                                ->where('muc_do', $muc);
                            if ($loaiDe === 'trac_nghiem') {
                                $cauHoiQuery = $cauHoiQuery->where('phan_loai', 0);
                            } elseif ($loaiDe === 'tu_luan_van_dap') {
                                $cauHoiQuery = $cauHoiQuery->whereIn('phan_loai', [1,2]);
                            }
                            $cauHois = $cauHoiQuery->with('dapAns')->inRandomOrder()->limit($soCau)->get();
                            foreach ($cauHois as $cau) {
                                $de[] = [
                                    'id' => $cau->id,
                                    'noi_dung' => $cau->cau_hoi,
                                    'id_chuong' => $cau->id_chuong,
                                    'id_chuan_dau_ra' => $cau->id_chuan_dau_ra,
                                    'muc_do' => $cau->muc_do,
                                    'phan_loai' => $cau->phan_loai,
                                    'dap_ans' => $cau->dapAns->map(function($da) {
                                        return [
                                            'id' => $da->id,
                                            'noi_dung' => $da->dap_an,
                                            'is_dap_an' => $da->trang_thai == 1
                                        ];
                                    })
                                ];
                            }
                        }
                    }
                }
            }
            $dsDe[] = $de;
        }
        // Lấy bộ đề cần xuất
        $de = $dsDe[$deIndex] ?? [];

        // Tạo file Word với format giống template mẫu tự luận/vấn đáp
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(13);
        $section = $phpWord->addSection();
        // Tiêu đề đầu trang
        $section->addText('TRƯỜNG ĐẠI HỌC NHA TRANG', ['bold' => true]);
        $section->addText('KHOA/VIỆN: ............................');
        $section->addText('BỘ MÔN: ...............................');
        $section->addTextBreak(1);
        $section->addText('BẢNG NGÂN HÀNG CÂU HỎI THI, ĐÁP ÁN VÀ THANG ĐIỂM', ['bold' => true, 'size' => 14], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('(Dùng cho câu hỏi thi tự luận, vấn đáp)', ['italic' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addTextBreak(1);
        $section->addText('Tên HP: ' . ($hocPhan->ten ?? '....................................................'));
        $section->addText('Tác giả biên soạn NHCHT và đáp án: ............................................................');
        $section->addTextBreak(1);

        // Bảng
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        // Tiêu đề bảng
        $table->addRow();
        $table->addCell(800)->addText('Câu hỏi', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(6000)->addText('Nội dung', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(1200)->addText('Điểm\n(Mỗi ý từ 0.25 - 0.5 đ)', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(1200)->addText('Độ khó\n(Dễ, Trung bình, Khó)', ['bold' => true], ['alignment' => 'center']);

        $tongDiem = 0;
        $sttChuong = 1;
        foreach ($chuongs as $chuong) {
            $table->addRow();
            $table->addCell(null, ['gridSpan' => 4])->addText(
                $this->toRoman($sttChuong) . '. Chương/Chủ đề',
                ['bold' => true]
            );
            $cdrsChuong = $chuong->chuanDauRas;
            $sttChuong++;
            foreach ($cdrsChuong as $cdr) {
                // Lấy các câu hỏi thuộc chương này và CĐR này
                $cauHoiTheoCDR = collect($de)->filter(function($cau) use ($chuong, $cdr) {
                    return $cau['id_chuong'] == $chuong->id && $cau['id_chuan_dau_ra'] == $cdr->id;
                })->values();
                if ($cauHoiTheoCDR->isEmpty()) continue;
                $table->addRow();
                $table->addCell(null, ['gridSpan' => 4])->addText('Chuẩn đầu ra ' . $cdr->id . ', Số lượng câu hỏi: ' . $cauHoiTheoCDR->count());
                $sttCau = 1;
                foreach ($cauHoiTheoCDR as $cau) {
                    $table->addRow();
                    $table->addCell(800)->addText($sttCau);
                    $table->addCell(6000)->addText('Câu hỏi: ' . $cau['noi_dung']);
                    $table->addCell(1200)->addText(number_format((float)($cau['diem'] ?? 0), 2) . ' đ');
                    $table->addCell(1200)->addText($cau['muc_do'] == 1 ? 'Dễ' : ($cau['muc_do'] == 2 ? 'Trung bình' : 'Khó'));
                    $sttCau++;
                    // Đáp án
                    $table->addRow();
                    $table->addCell(800)->addText('Đáp án:', ['italic' => true]);
                    $y = 1;
                    foreach ($cau['dap_ans'] as $da) {
                        $table->addCell(6000)->addText('Nội dung ý ' . $y . ': ' . $da['noi_dung']);
                        $table->addCell(1200)->addText(number_format((float)($da['diem'] ?? 0), 2) . ' đ');
                        $table->addCell(1200)->addText('');
                        $y++;
                        $tongDiem += (float)($da['diem'] ?? 0);
                        if ($y > 4) break; // chỉ tối đa 4 ý
                    }
                }
            }
        }
        // Tổng điểm
        $table->addRow();
        $table->addCell(null, ['gridSpan' => 2])->addText('Tổng điểm', ['bold' => true]);
        $table->addCell(1200)->addText(number_format($tongDiem, 2) . ' đ', ['bold' => true]);
        $table->addCell(1200)->addText('');

        // Phần ký tên cuối trang
        $section->addTextBreak(2);
        $section->addText('TRƯỞNG BM/KHOA/VIỆN', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END]);
        $section->addText('(Ký và ghi rõ họ tên)', ['italic' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END]);

        $fileName = 'de_' . ($deIndex+1) . '_day_du_' . $hocPhan->id . '.docx';
        $tempPath = storage_path('app/temp/' . $fileName);
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);
        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    public function exportDownloadFull(Request $request, $id)
    {
        $deIndex = (int) $request->query('de', 1) - 1;
        $soDe = $request->input('so_de') ?? 1;
        $loaiDe = $request->input('loai_de') ?? '';

        // Lấy lại dữ liệu export như cũ
        $hocPhan = HocPhan::with(['chuongs.chuanDauRas', 'chuanDauRas', 'chuongs.cauHois'])->findOrFail($id);
        $chuongs = $hocPhan->chuongs;
        $cdrs = $hocPhan->chuanDauRas;
        $giao = [];
        foreach ($chuongs as $ch) {
            foreach ($ch->chuongChuanDauRa as $pivot) {
                $giao[] = [$ch->id, $pivot->id_chuan_dau_ra];
            }
        }
        $bang = [];
        foreach ($giao as [$chuongId, $cdrId]) {
            $row = MaTran::where('id_chuong', $chuongId)->where('id_chuan_dau_ra', $cdrId)->first();
            $bang[$chuongId][$cdrId] = [
                1 => $row ? $row->so_cau_de : 0,
                2 => $row ? $row->so_cau_tb : 0,
                3 => $row ? $row->so_cau_kho : 0,
            ];
        }
        $dsDe = [];
        for ($i = 1; $i <= $soDe; $i++) {
            $de = [];
            foreach ($bang as $chuongId => $cdrArr) {
                foreach ($cdrArr as $cdrId => $mucArr) {
                    foreach ([1,2,3] as $muc) {
                        $soCau = $mucArr[$muc] ?? 0;
                        if ($soCau > 0) {
                            $cauHoiQuery = $chuongs->find($chuongId)?->cauHois()
                                ->where('id_chuan_dau_ra', $cdrId)
                                ->where('muc_do', $muc);
                            if ($loaiDe === 'trac_nghiem') {
                                $cauHoiQuery = $cauHoiQuery->where('phan_loai', 0);
                            } elseif ($loaiDe === 'tu_luan_van_dap') {
                                $cauHoiQuery = $cauHoiQuery->whereIn('phan_loai', [1,2]);
                            }
                            $cauHois = $cauHoiQuery->with('dapAns')->inRandomOrder()->limit($soCau)->get();
                            foreach ($cauHois as $cau) {
                                $de[] = [
                                    'id' => $cau->id,
                                    'noi_dung' => $cau->cau_hoi,
                                    'id_chuong' => $cau->id_chuong,
                                    'id_chuan_dau_ra' => $cau->id_chuan_dau_ra,
                                    'muc_do' => $cau->muc_do,
                                    'phan_loai' => $cau->phan_loai,
                                    'dap_ans' => $cau->dapAns->map(function($da) {
                                        return [
                                            'id' => $da->id,
                                            'noi_dung' => $da->dap_an,
                                            'is_dap_an' => $da->trang_thai == 1
                                        ];
                                    })
                                ];
                            }
                        }
                    }
                }
            }
            $dsDe[] = $de;
        }
        // Lấy bộ đề cần xuất
        $de = $dsDe[$deIndex] ?? [];
        
        // Tạo file Word với format đầy đủ
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        
        // Thêm tiêu đề cột
        $table->addRow();
        $table->addCell(800)->addText('STT');
        $table->addCell(2000)->addText('Chương');
        $table->addCell(2000)->addText('Chuẩn đầu ra');
        $table->addCell(4000)->addText('Nội dung câu hỏi');
        $table->addCell(2000)->addText('Đáp án A');
        $table->addCell(2000)->addText('Đáp án B');
        $table->addCell(2000)->addText('Đáp án C');
        $table->addCell(2000)->addText('Đáp án D');
        $table->addCell(2000)->addText('Đáp án đúng');
        $table->addCell(1000)->addText('Điểm');
        $table->addCell(1000)->addText('Mức độ');
        $table->addCell(1500)->addText('Loại đề');

        foreach ($de as $stt => $cau) {
            $chuong = $chuongs->find($cau['id_chuong']);
            $cdr = $cdrs->find($cau['id_chuan_dau_ra']);
            $table->addRow();
            $table->addCell(800)->addText($stt + 1);
            $table->addCell(2000)->addText($chuong ? $chuong->ten : '');
            $table->addCell(2000)->addText($cdr ? ($cdr->ten ?? $cdr->mo_ta ?? '') : '');
            $table->addCell(4000)->addText($cau['noi_dung']);

            $dapan = $cau['dap_ans'];
            if ($cau['phan_loai'] == 0) {
                // Trắc nghiệm: điền đáp án A-D
                for ($i = 0; $i < 4; $i++) {
                    $table->addCell(2000)->addText($dapan[$i]['noi_dung'] ?? '');
                }
                // Đáp án đúng
                $dapanDung = '';
                foreach ($dapan as $j => $da) {
                    if ($da['is_dap_an']) $dapanDung .= chr(65 + $j) . ' ';
                }
                $table->addCell(2000)->addText(trim($dapanDung));
            } else {
                // Tự luận/vấn đáp: các cột đáp án A-D để trống, đáp án đúng là đáp án mẫu
                for ($i = 0; $i < 4; $i++) {
                    $table->addCell(2000)->addText('');
                }
                $table->addCell(2000)->addText(
                    collect($dapan)->pluck('noi_dung')->implode('; ')
                );
            }

            $table->addCell(1000)->addText($cau['diem'] ?? '');
            $table->addCell(1000)->addText($cau['muc_do'] == 1 ? 'Dễ' : ($cau['muc_do'] == 2 ? 'Trung bình' : 'Khó'));
            $table->addCell(1500)->addText($cau['phan_loai'] == 0 ? 'Trắc nghiệm' : ($cau['phan_loai'] == 1 ? 'Tự luận' : 'Vấn đáp'));
        }

        $fileName = 'de_' . ($deIndex+1) . '_day_du_' . $hocPhan->id . '.docx';
        $tempPath = storage_path('app/temp/' . $fileName);
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);
        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    public function exportDownloadSimple(Request $request, $id)
    {
        $deIndex = (int) $request->query('de', 1) - 1;
        $soDe = $request->input('so_de') ?? 1;
        $loaiDe = $request->input('loai_de') ?? '';

        // Lấy lại dữ liệu export như cũ
        $hocPhan = HocPhan::with(['chuongs.chuanDauRas', 'chuanDauRas', 'chuongs.cauHois'])->findOrFail($id);
        $chuongs = $hocPhan->chuongs;
        $cdrs = $hocPhan->chuanDauRas;
        $giao = [];
        foreach ($chuongs as $ch) {
            foreach ($ch->chuongChuanDauRa as $pivot) {
                $giao[] = [$ch->id, $pivot->id_chuan_dau_ra];
            }
        }
        $bang = [];
        foreach ($giao as [$chuongId, $cdrId]) {
            $row = MaTran::where('id_chuong', $chuongId)->where('id_chuan_dau_ra', $cdrId)->first();
            $bang[$chuongId][$cdrId] = [
                1 => $row ? $row->so_cau_de : 0,
                2 => $row ? $row->so_cau_tb : 0,
                3 => $row ? $row->so_cau_kho : 0,
            ];
        }
        $dsDe = [];
        for ($i = 1; $i <= $soDe; $i++) {
            $de = [];
            foreach ($bang as $chuongId => $cdrArr) {
                foreach ($cdrArr as $cdrId => $mucArr) {
                    foreach ([1,2,3] as $muc) {
                        $soCau = $mucArr[$muc] ?? 0;
                        if ($soCau > 0) {
                            $cauHoiQuery = $chuongs->find($chuongId)?->cauHois()
                                ->where('id_chuan_dau_ra', $cdrId)
                                ->where('muc_do', $muc);
                            if ($loaiDe === 'trac_nghiem') {
                                $cauHoiQuery = $cauHoiQuery->where('phan_loai', 0);
                            } elseif ($loaiDe === 'tu_luan_van_dap') {
                                $cauHoiQuery = $cauHoiQuery->whereIn('phan_loai', [1,2]);
                            }
                            $cauHois = $cauHoiQuery->with('dapAns')->inRandomOrder()->limit($soCau)->get();
                            foreach ($cauHois as $cau) {
                                $de[] = [
                                    'id' => $cau->id,
                                    'noi_dung' => $cau->cau_hoi,
                                    'id_chuong' => $cau->id_chuong,
                                    'id_chuan_dau_ra' => $cau->id_chuan_dau_ra,
                                    'muc_do' => $cau->muc_do,
                                    'phan_loai' => $cau->phan_loai,
                                    'dap_ans' => $cau->dapAns->map(function($da) {
                                        return [
                                            'id' => $da->id,
                                            'noi_dung' => $da->dap_an,
                                            'is_dap_an' => $da->trang_thai == 1
                                        ];
                                    })
                                ];
                            }
                        }
                    }
                }
            }
            $dsDe[] = $de;
        }
        // Lấy bộ đề cần xuất
        $de = $dsDe[$deIndex] ?? [];
        
        // Tạo file Word với format đơn giản
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText('ĐỀ THI - ' . ($hocPhan->ten ?? $hocPhan->id), ['bold' => true, 'size' => 16]);
        $section->addText('Mã học phần: ' . $hocPhan->id);
        $section->addText('Tên học phần: ' . $hocPhan->ten);
        $section->addTextBreak(1);

        foreach ($de as $i => $cau) {
            $section->addText('Câu ' . ($i+1) . ': ' . $cau['noi_dung']);
            if ($cau['phan_loai'] == 0) {
                // Trắc nghiệm
                foreach ($cau['dap_ans'] as $j => $da) {
                    $txt = chr(65 + $j) . '. ' . $da['noi_dung'];
                    $section->addText($txt);
                }
            } else {
                // Tự luận/vấn đáp
                foreach ($cau['dap_ans'] as $j => $da) {
                    $txt = 'Ý ' . ($j+1) . '. ' . $da['noi_dung'];
                    $section->addText($txt);
                }
            }
            $section->addTextBreak(1);
        }

        $fileName = 'de_' . ($deIndex+1) . '_don_gian_' . $hocPhan->id . '.docx';
        $tempPath = storage_path('app/temp/' . $fileName);
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);
        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }
} 