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
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\IOFactory;
use App\Models\DSGVBienSoan;
use App\Models\CTDSDangKy;

class MatranController extends Controller
{
    /**
     * Kiểm tra quyền quản lý của người dùng với học phần
     * 
     * @param string $hocPhanId ID của học phần cần kiểm tra
     * @return bool true nếu người dùng có quyền quản lý, ngược lại false
     */
    private function userCanManageHocPhan($hocPhanId)
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name');

        // Admin hoặc Trưởng Bộ Môn có quyền quản lý tất cả
        if ($roles->contains('Admin') || $roles->contains('Trưởng Bộ Môn')) {
            return true;
        }

        // Giảng viên chỉ có quyền quản lý học phần mà họ tham gia biên soạn
        if ($roles->contains('Giảng viên')) {
            return DSGVBienSoan::whereHas('ctDSDangKy', function($query) use ($hocPhanId) {
                $query->where('id_hoc_phan', $hocPhanId)->where('able', true);
            })->where('id_vien_chuc', $user->id)->where('able', true)->exists();
        }

        return false;
    }

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
        $user = Auth::user();
        $roles = $user->roles->pluck('name');
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
        
        $loai_ky = $request->query('loai_ky', 'cuoi_ky');
        
        $ctDangKyIds = DSGVBienSoan::where('id_vien_chuc', $user->id)
                ->where('able', true)
                ->pluck('id_ct_ds_dang_ky');
            
            $hocPhanIds = CTDSDangKy::whereIn('id', $ctDangKyIds)
                ->where('able', true)
                ->pluck('id_hoc_phan');
                
            $dsHocPhan = HocPhan::with('chuongs', 'chuanDauRas')
                ->whereIn('id', $hocPhanIds)
                ->where('able', true)
                ->get();
        
        $chuongIdsDaCoMaTran = MaTran::where('loai_ky', $loai_ky)
                              ->where('able', true)
                              ->distinct()
                              ->pluck('id_chuong')
                              ->toArray();

        // Lấy danh sách id_hoc_phan của các chương này
        $hpDaCoMaTran = Chuong::whereIn('id', $chuongIdsDaCoMaTran)
            ->where('able', true)
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
            
            // Kiểm tra quyền truy cập học phần
            if ($hocPhan && $this->userCanManageHocPhan($hocPhan->id)) {
                $chuongs = $hocPhan->chuongs->where('able', true)->values() ?? collect([]);
                $cdrs = $hocPhan->chuanDauRas->where('able', true)->values() ?? collect([]);
                // Lấy các cặp giao giữa chương và CDR (bảng chuong_chuan_dau_ra)
                $giao = [];
                foreach ($chuongs as $ch) {
                    foreach ($ch->chuongChuanDauRa->where('able', true) as $pivot) {
                        $giao[] = [$ch->id, $pivot->id_chuan_dau_ra];
                    }
                }
                // Sau khi lấy $chuongs, $cdrs, $giao
                Log::info('chuongs', $chuongs->toArray());
                Log::info('cdrs', $cdrs->toArray());
                Log::info('giao', $giao);
            } else {
                return redirect()->route('matran.index')
                    ->with('error', 'Bạn không có quyền quản lý ma trận cho học phần này!');
            }
        }
        
        return Inertia::render('TBM/Matran/Create', [
            'hocPhans' => $hocPhans,
            'chuongs' => $chuongs,
            'cdrs' => $cdrs,
            'giao' => $giao,
            'selectedHocPhan' => $request->hoc_phan_id ?? null,
            'loai_ky' => $loai_ky,
            'role' => $role
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
                'loai_ky' => 'required|in:giua_ky,cuoi_ky',
            ]);

            // Kiểm tra quyền quản lý học phần
            if (!$this->userCanManageHocPhan($request->input('hoc_phan'))) {
                return back()->withErrors(['error' => 'Bạn không có quyền quản lý ma trận cho học phần này!']);
            }

            // Kiểm tra xem học phần đã có ma trận chưa
            $chuongIds = array_keys($request->input('bang'));
            // Lấy id_hoc_phan từ bảng chuongs
            $chuongs = Chuong::whereIn('id', $chuongIds)->get();
            $existingMatran = MaTran::whereIn('id_chuong', $chuongIds)
                ->whereIn('id_chuong', $chuongs->pluck('id'))
                ->where('loai_ky', $request->input('loai_ky'))
                ->exists();
            
            if ($existingMatran) {
                return back()->withErrors(['error' => 'Học phần này đã có ma trận ' . ($request->input('loai_ky') == 'giua_ky' ? 'giữa kỳ' : 'cuối kỳ') . '!']);
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
                        'loai_ky' => $request->input('loai_ky'),
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
                    'loai_ky' => $request->input('loai_ky'),
                    'so_dong' => count($data)
                ]);
                
                return redirect()->route('matran.index')
                    ->with('success', 'Tạo ma trận ' . ($request->input('loai_ky') == 'giua_ky' ? 'giữa kỳ' : 'cuối kỳ') . ' thành công!');
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
        $user = Auth::user();
        $roles = $user->roles->pluck('name');
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
        
        $loai_ky = $request->query('loai_ky', 'cuoi_ky');
        
        $allHocPhans = HocPhan::where('able', true)->select('id', 'ten')->orderBy('ten')->get();

        // Lấy các học phần có ma trận tùy theo loại kỳ
        $hocPhanIds = Chuong::whereHas('maTrans', function($query) use ($loai_ky) {
            $query->where('loai_ky', $loai_ky)->where('able', true);
        })->where('able', true)->pluck('id_hoc_phan')->unique();
        
        $query = HocPhan::whereIn('id', $hocPhanIds)
            ->where('able', true)
            ->withCount(['chuongs'])
            ->orderBy('ten');

            $ctDangKyIds = DSGVBienSoan::where('id_vien_chuc', $user->id)
            ->where('able', true)
            ->pluck('id_ct_ds_dang_ky');
            
        $gvHocPhanIds = CTDSDangKy::whereIn('id', $ctDangKyIds)
            ->where('able', true)
            ->pluck('id_hoc_phan');
            
        $query->whereIn('id', $gvHocPhanIds);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(ten) LIKE ?', ['%' . mb_strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(id) LIKE ?', ['%' . mb_strtolower($search) . '%']);
            });
        }

        $hocPhans = $query->get();
        Log::info('hocPhans', $hocPhans->toArray());

        return Inertia::render('TBM/Matran/Index', [
            'allHocPhans' => $allHocPhans,
            'hocPhans' => $hocPhans,
            'filters' => $request->only(['search']),
            'loai_ky' => $loai_ky,
            'role' => $role
        ]);
    }

    public function show($id, Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name');
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
        
        $loai_ky = $request->query('loai_ky', 'cuoi_ky');
        
        // Kiểm tra quyền quản lý học phần
        if (!$this->userCanManageHocPhan($id)) {
            return redirect()->route('matran.index')
                ->with('error', 'Bạn không có quyền xem ma trận cho học phần này!');
        }
        
        // Lấy học phần, chương, CDR, các cặp giao, và dữ liệu ma trận
        $hocPhan = HocPhan::with(['chuongs.chuanDauRas', 'chuanDauRas'])->where('able', true)->findOrFail($id);
        $chuongs = $hocPhan->chuongs->where('able', true)->values() ?? collect([]);
        $cdrs = $hocPhan->chuanDauRas->where('able', true)->values() ?? collect([]);
        $giao = [];
        foreach ($chuongs as $ch) {
            foreach ($ch->chuongChuanDauRa->where('able', true) as $pivot) {
                $giao[] = [$ch->id, $pivot->id_chuan_dau_ra];
            }
        }
        
        Log::info('GIAO_SHOW', $giao);
        $bang = [];
        foreach ($giao as [$chuongId, $cdrId]) {
            $row = MaTran::where('id_chuong', $chuongId)
                ->where('id_chuan_dau_ra', $cdrId)
                ->where('loai_ky', $loai_ky)
                ->where('able', true)
                ->first();
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
            'id' => $id,
            'loai_ky' => $loai_ky,
            'role' => $role
        ]);
    }

    public function edit($id, Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name');
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
        
        $loai_ky = $request->query('loai_ky', 'cuoi_ky');
        
        // Kiểm tra quyền quản lý học phần
        if (!$this->userCanManageHocPhan($id)) {
            return redirect()->route('matran.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa ma trận cho học phần này!');
        }
        
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
            $row = MaTran::where('id_chuong', $chuongId)
                ->where('id_chuan_dau_ra', $cdrId)
                ->where('loai_ky', $loai_ky)
                ->first();
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
            'id' => $id,
            'loai_ky' => $loai_ky,
            'role' => $role
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
            'loai_ky' => 'required|in:giua_ky,cuoi_ky',
        ]);
        
        // Kiểm tra quyền quản lý học phần
        if (!$this->userCanManageHocPhan($id)) {
            return redirect()->route('matran.index')
                ->with('error', 'Bạn không có quyền cập nhật ma trận cho học phần này!');
        }

        $bang = $request->input('bang');
        $loai_ky = $request->input('loai_ky');

        // Cập nhật từng dòng ma trận
        foreach ($bang as $chuongId => $cdrArr) {
            foreach ($cdrArr as $cdrId => $mucArr) {
                $row = MaTran::where('id_chuong', $chuongId)
                    ->where('id_chuan_dau_ra', $cdrId)
                    ->where('loai_ky', $loai_ky)
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

        return redirect()->route('matran.index')->with('success', 'Cập nhật ma trận ' . ($loai_ky == 'giua_ky' ? 'giữa kỳ' : 'cuối kỳ') . ' thành công!');
    }

    public function export(Request $request, $id)
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name');
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
        
        // Kiểm tra quyền quản lý học phần
        if (!$this->userCanManageHocPhan($id)) {
            return redirect()->route('matran.index')
                ->with('error', 'Bạn không có quyền xuất ma trận cho học phần này!');
        }
        
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
                                        'diem' => $cau->diem,
                                        'dap_ans' => $cau->dapAns->map(function($da) {
                                            return [
                                                'id' => $da->id,
                                                'noi_dung' => $da->dap_an,
                                                'diem' => $da->diem,
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
            'loaiDe' => $loaiDe,
            'role' => $role
        ]);
    }

    public function exportDownloadFull(Request $request, $id)
    {
        // Kiểm tra quyền quản lý học phần
        if (!$this->userCanManageHocPhan($id)) {
            return redirect()->route('matran.index')
                ->with('error', 'Bạn không có quyền tải xuống ma trận cho học phần này!');
        }
        
        // Lấy các tham số từ request
        $loaiDe = $request->input('loai_de') ?? '';
        $loai_ky = $request->input('loai_ky') ?? 'cuoi_ky';
        $kyThiText = $loai_ky == 'giua_ky' ? 'Giữa kỳ' : 'Cuối kỳ';

        // Lấy lại dữ liệu export
        $hocPhan = HocPhan::with(['chuongs.chuanDauRas', 'chuanDauRas', 'chuongs.cauHois', 'boMon.khoa'])->findOrFail($id);
        $chuongs = $hocPhan->chuongs;
        $cdrs = $hocPhan->chuanDauRas;
        $dsCauHoi = $request->input('dsCauHoi') ?? [];
        
        // Tạo file Word với cấu trúc giống template
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);
        
        $section = $phpWord->addSection();
        
        // Style cho bảng
        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 100,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER
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
        
    
        // Thêm thông tin trường, khoa, bộ môn
        $headerTableStyle = [
            'borderSize' => 0,
            'cellMargin' => 100
        ];
        
        $headerTable = $section->addTable($headerTableStyle);
        $headerTable->addRow();
        $headerTable->addCell(4000, ['borderSize' => 0])->addText('TRƯỜNG ĐẠI HỌC NHA TRANG', $boldTextStyle);
        $headerTable->addRow();
        
        // Thêm tên khoa nếu có
        $khoaText = $hocPhan->boMon && $hocPhan->boMon->khoa ? 'Khoa/Viện: ' . $hocPhan->boMon->khoa->ten : 'Khoa/Viện: .............................';
        $headerTable->addCell(4000, ['borderSize' => 0])->addText($khoaText, $boldTextStyle);
        $headerTable->addRow();
        
        // Thêm tên bộ môn nếu có
        $boMonText = $hocPhan->boMon ? 'Bộ môn: ' . $hocPhan->boMon->ten : 'Bộ môn: ..................................';
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
        
        // Thêm phụ đề tùy thuộc vào loại đề
        $isTracNghiem = ($loaiDe === 'trac_nghiem');
        if ($isTracNghiem) {
            $section->addText('(Dùng cho câu hỏi thi trắc nghiệm)', $fontStyleItalic, $paragraphStyleSubtitle);
        } else {
            $section->addText('(Dùng cho câu hỏi thi tự luận, vấn đáp)', $fontStyleItalic, $paragraphStyleSubtitle);
        }
        $section->addTextBreak(1);
        
        // Thêm thông tin HP
        $hocPhanText = 'Tên HP: ' . ($hocPhan ? $hocPhan->ten : '......................................');
        $section->addText($hocPhanText);
        $section->addText('Loại kỳ thi: ' . $kyThiText);
        
        // Lấy thông tin giảng viên biên soạn
        $dsGVBienSoans = [];
        $ctDangKys = CTDSDangKy::where('id_hoc_phan', $id)->with('dsGvBienSoans.vienChuc')->get();
        foreach ($ctDangKys as $ctDangKy) {
            foreach ($ctDangKy->dsGvBienSoans as $gvbs) {
                if ($gvbs->vienChuc && !in_array($gvbs->vienChuc->name, $dsGVBienSoans)) {
                    $dsGVBienSoans[] = $gvbs->vienChuc->name;
                }
            }
        }
        $gvBienSoanNames = !empty($dsGVBienSoans) ? implode(', ', $dsGVBienSoans) : '............................................................';
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
        $table->addCell(1000, )->addText('STT');
        $table->addCell(4000)->addText('Nội dung');
        $table->addCell(1500)->addText('Điểm');
        $table->addCell(1500)->addText('Mức độ');

        // Sắp xếp các câu hỏi theo chương và CDR
        $cauHoiTheoChuong = [];
        foreach ($dsCauHoi as $cau) {
            $idChuong = $cau['id_chuong'];
            $idCDR = $cau['id_chuan_dau_ra'];
            
            if (!isset($cauHoiTheoChuong[$idChuong])) {
                $cauHoiTheoChuong[$idChuong] = [];
            }
            
            if (!isset($cauHoiTheoChuong[$idChuong][$idCDR])) {
                $cauHoiTheoChuong[$idChuong][$idCDR] = [];
            }
            
            $cauHoiTheoChuong[$idChuong][$idCDR][] = $cau;
        }
        
        // Tổng điểm toàn bộ đề thi
        $tongDiemDeThi = 0;
        
        // Số thứ tự câu hỏi toàn đề
        $sttCauHoiToanDe = 1;
        
        // Hiển thị các chương và câu hỏi
        $sttChuong = 1;
        foreach ($chuongs as $chuong) {
            // Bỏ qua chương không có câu hỏi
            if (!isset($cauHoiTheoChuong[$chuong->id])) continue;
            
            // Sử dụng số La Mã
            $chapterNumber = $this->toRoman($sttChuong);
            
            // Tiêu đề chương
            $table->addRow();
            $cell = $table->addCell(8000, ['gridSpan' => 4]);
            $cell->addText("$chapterNumber. Chương/Chủ đề " . $chuong->ten, $boldTextStyle);
            
            // Hiển thị các chuẩn đầu ra và câu hỏi
            foreach ($cdrs as $cdr) {
                // Bỏ qua CDR không có câu hỏi trong chương này
                if (!isset($cauHoiTheoChuong[$chuong->id][$cdr->id])) continue;
                
                $cauHoiTheoCDR = $cauHoiTheoChuong[$chuong->id][$cdr->id];
                
                // Tiêu đề chuẩn đầu ra
                $table->addRow();
                $cell = $table->addCell(8000, ['gridSpan' => 4]);
                $cell->addText("Chuẩn đầu ra " . $cdr->ten . ", Số lượng câu hỏi: " . count($cauHoiTheoCDR));
                
                // Hiển thị từng câu hỏi
                foreach ($cauHoiTheoCDR as $cau) {
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
                    
                    $cell->addText($sttCauHoiToanDe, $fontStyle, $paragraphStyle);
                    
                    // Nội dung câu hỏi
                    $table->addCell(4000, $cellStyle)->addText('Câu hỏi: ' . $cau['noi_dung']);
                    
                    // Điểm
                    $table->addCell(1500, $cellStyle)->addText(number_format($cau['diem'], 2) . ' đ');
                    
                    // Mức độ
                    $mucDoText = $cau['muc_do'] == 1 ? 'Dễ' : ($cau['muc_do'] == 2 ? 'Trung bình' : 'Khó');
                    $table->addCell(1500, $cellStyle)->addText($mucDoText);
                    
                    // Đáp án
                    $table->addRow();
                    $table->addCell(1000, ['vMerge' => 'continue']);
                    $table->addCell(4000, $cellStyle)->addText('Đáp án:');
                    $table->addCell(1500, $cellStyle);
                    $table->addCell(1500, $cellStyle);
                    
                    // Cập nhật tổng điểm
                    $tongDiemDeThi += (float)$cau['diem'];
                    
                    // Các đáp án
                    $dapAnList = $cau['dap_ans'];
                    
                    if ($cau['phan_loai'] == 0) { // Trắc nghiệm
                        // Hiển thị các lựa chọn trắc nghiệm
                        $options = ['A', 'B', 'C', 'D'];
                        foreach ($dapAnList as $index => $dapAn) {
                            if ($index >= count($options)) break; // Giới hạn 4 đáp án
                            
                            $table->addRow();
                            $table->addCell(1000, ['vMerge' => 'continue']);
                            $table->addCell(4000, $cellStyle)->addText($options[$index] . '. ' . $dapAn['noi_dung']);
                            
                            // Hiển thị điểm cho đáp án
                            $diemDapAn = $dapAn['is_dap_an'] ? number_format($dapAn['diem'], 2) . ' đ' : '0.00 đ';
                            $table->addCell(1500, $cellStyle)->addText($diemDapAn);
                            $table->addCell(1500, $cellStyle);
                        }
                    } else { // Tự luận hoặc vấn đáp
                        foreach ($dapAnList as $index => $dapAn) {
                            $table->addRow();
                            $table->addCell(1000, ['vMerge' => 'continue']);
                            $table->addCell(4000, $cellStyle)->addText('Nội dung ý ' . ($index + 1) . ': ' . $dapAn['noi_dung']);
                            $table->addCell(1500, $cellStyle)->addText(number_format($dapAn['diem'], 2) . ' đ');
                            $table->addCell(1500, $cellStyle);
                        }
                    }
                    
                    // Tăng số thứ tự câu hỏi toàn đề
                    $sttCauHoiToanDe++;
                }
            }
            
            $sttChuong++;
        }
        
        // Tổng điểm
        $table->addRow();
        $table->addCell(5000, ['gridSpan' => 2, 'valign' => 'center'])->addText('Tổng điểm', ['alignment' => 'center']);
        $table->addCell(1500, $cellStyle)->addText(number_format($tongDiemDeThi, 2) . ' đ');
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
        
        // Tạo tên file
        $fileName = $kyThiText . '_de_' . $hocPhan->id . '.docx';
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
       // Kiểm tra quyền quản lý học phần
       if (!$this->userCanManageHocPhan($id)) {
        return redirect()->route('matran.index')
            ->with('error', 'Bạn không có quyền tải xuống ma trận cho học phần này!');
    }
    $dsCauHoi = $request->input('dsCauHoi') ?? [];
    Log::info('adsCauHoi', $dsCauHoi);
    
    // Lấy các tham số từ request
    $loaiDe = $request->input('loai_de') ?? '';
    $loai_ky = $request->input('loai_ky') ?? 'cuoi_ky';
    $kyThiText = $loai_ky == 'giua_ky' ? 'Giữa kỳ' : 'Cuối kỳ';

    // Lấy lại dữ liệu export
    $hocPhan = HocPhan::with(['chuongs.chuanDauRas', 'chuanDauRas', 'chuongs.cauHois', 'boMon.khoa'])->findOrFail($id);
    $chuongs = $hocPhan->chuongs;
    $cdrs = $hocPhan->chuanDauRas;
    $dsCauHoi = $request->input('dsCauHoi') ?? [];
    
    // Tạo file Word với cấu trúc giống template
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $phpWord->setDefaultFontName('Times New Roman');
    $phpWord->setDefaultFontSize(12);
    
    $section = $phpWord->addSection();
    
    // Style cho bảng
    $tableStyle = [
        'borderSize' => 6,
        'borderColor' => '000000',
        'cellMargin' => 100,
        'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER
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
    

    // Thêm thông tin trường, khoa, bộ môn
    $headerTableStyle = [
        'borderSize' => 0,
        'cellMargin' => 100
    ];
    
    $headerTable = $section->addTable($headerTableStyle);
    $headerTable->addRow();
    $headerTable->addCell(4000, ['borderSize' => 0])->addText('TRƯỜNG ĐẠI HỌC NHA TRANG', $boldTextStyle);
    $headerTable->addRow();
    
    // Thêm tên khoa nếu có
    $khoaText = $hocPhan->boMon && $hocPhan->boMon->khoa ? 'Khoa/Viện: ' . $hocPhan->boMon->khoa->ten : 'Khoa/Viện: .............................';
    $headerTable->addCell(4000, ['borderSize' => 0])->addText($khoaText, $boldTextStyle);
    $headerTable->addRow();
    
    // Thêm tên bộ môn nếu có
    $boMonText = $hocPhan->boMon ? 'Bộ môn: ' . $hocPhan->boMon->ten : 'Bộ môn: ..................................';
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
    
    // Tương tự cho phụ đề
    $fontStyleItalic = new \PhpOffice\PhpWord\Style\Font();
    $fontStyleItalic->setItalic(true);
    $fontStyleItalic->setName('Times New Roman');
    $fontStyleItalic->setSize(13);
    
    $paragraphStyleSubtitle = new \PhpOffice\PhpWord\Style\Paragraph();
    $paragraphStyleSubtitle->setAlignment(\PhpOffice\PhpWord\SimpleType\Jc::CENTER);
    $paragraphStyleSubtitle->setSpaceAfter(100);
    
    // Áp dụng cả paragraph style và font style
    $section->addText( $hocPhan->ten, $fontStyle, $paragraphStyle);
    
    
    // Thêm phụ đề tùy thuộc vào loại đề
    $isTracNghiem = ($loaiDe === 'trac_nghiem');
    if ($isTracNghiem) {
        $section->addText('(Dùng cho câu hỏi thi trắc nghiệm)', $fontStyleItalic, $paragraphStyleSubtitle);
    } else {
        $section->addText('(Dùng cho câu hỏi thi tự luận, vấn đáp)', $fontStyleItalic, $paragraphStyleSubtitle);
    }
    
    // Lấy thông tin giảng viên biên soạn
    $dsGVBienSoans = [];
    $ctDangKys = CTDSDangKy::where('id_hoc_phan', $id)->with('dsGvBienSoans.vienChuc')->get();
    foreach ($ctDangKys as $ctDangKy) {
        foreach ($ctDangKy->dsGvBienSoans as $gvbs) {
            if ($gvbs->vienChuc && !in_array($gvbs->vienChuc->name, $dsGVBienSoans)) {
                $dsGVBienSoans[] = $gvbs->vienChuc->name;
            }
        }
    }
    $gvBienSoanNames = !empty($dsGVBienSoans) ? implode(', ', $dsGVBienSoans) : '............................................................';
    $section->addText('Tác giả biên soạn NHCHT và đáp án: ' . $gvBienSoanNames);
    
    
     foreach ($dsCauHoi as $index => $cau) {
        $section->addText('Câu ' . ($index + 1) . ': ' . $cau['noi_dung'] . ' (' . $cau['diem'] . 'đ)', $boldTextStyle);
        $section->addTextBreak(1);
        if ($isTracNghiem) {
            $alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
            foreach ($cau['dap_ans'] as $index => $dapAn) {
                $section->addText( $alphabet[$index] . '. ' . $dapAn['noi_dung']);
            }
        } else {
           $section->addText('........................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................');
        }
        $section->addTextBreak(1);
     }
            
        
    
    // Tạo tên file
    $fileName = $kyThiText . '_de_' . $hocPhan->id . '.docx';
    $tempPath = storage_path('app/temp/' . $fileName);
    if (!file_exists(storage_path('app/temp'))) {
        mkdir(storage_path('app/temp'), 0755, true);
    }
    $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($tempPath);
    return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }
} 