<?php

namespace App\Http\Controllers\TBM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HocPhan;
use App\Models\Chuong;
use App\Models\MaTran;
use App\Models\ChuanDauRa;
use App\Models\ChuongChuanDauRa;
use App\Models\CTDSDangKy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class TrichXuatDeThiController extends Controller
{
    /**
     * Hiển thị danh sách học phần có ma trận để trích xuất đề thi
     */
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

        // Chỉ TBM mới có quyền truy cập
        if (!$roles->contains('Trưởng Bộ Môn')) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập trang này!');
        }

        $loai_ky = $request->query('loai_ky', 'cuoi_ky');
        
        // Lấy các học phần có ma trận thuộc bộ môn của TBM
        $hocPhanIds = Chuong::whereHas('maTrans', function($query) use ($loai_ky) {
            $query->where('loai_ky', $loai_ky)->where('able', true);
        })->where('able', true)->pluck('id_hoc_phan')->unique();
        
        $query = HocPhan::whereIn('id', $hocPhanIds)
            ->where('able', true)
            ->where('id_bo_mon', $user->id_bo_mon) // Chỉ lấy học phần thuộc bộ môn của TBM
            ->withCount(['chuongs'])
            ->orderBy('ten');

        // Tìm kiếm theo tên hoặc mã học phần
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(ten) LIKE ?', ['%' . mb_strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(id) LIKE ?', ['%' . mb_strtolower($search) . '%']);
            });
        }

        $hocPhans = $query->get();

        return Inertia::render('TBM/TrichXuatDeThi/Index', [
            'hocPhans' => $hocPhans,
            'filters' => $request->only(['search']),
            'loai_ky' => $loai_ky,
            'role' => $role
        ]);
    }

    /**
     * Hiển thị trang trích xuất đề thi cho học phần cụ thể
     */
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

        // Chỉ TBM mới có quyền truy cập
        if (!$roles->contains('Trưởng Bộ Môn') && !$roles->contains('Admin')) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập trang này!');
        }

        $loai_ky = $request->query('loai_ky', 'cuoi_ky');
        
        // Kiểm tra học phần có thuộc bộ môn của TBM không
        $hocPhan = HocPhan::with(['chuongs.chuanDauRas', 'chuanDauRas', 'chuongs.cauHois'])
            ->where('id', $id)
            ->where('id_bo_mon', $user->id_bo_mon)
            ->where('able', true)
            ->firstOrFail();
        
        $chuongs = $hocPhan->chuongs->where('able', true)->values() ?? collect([]);
        $cdrs = $hocPhan->chuanDauRas->where('able', true)->values() ?? collect([]);
        
        // Lấy các cặp giao giữa chương và CDR
        $giao = [];
        foreach ($chuongs as $ch) {
            foreach ($ch->chuongChuanDauRa->where('able', true) as $pivot) {
                $giao[] = [$ch->id, $pivot->id_chuan_dau_ra];
            }
        }
        
        // Tạo bảng ma trận
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

        // Xử lý trích xuất đề thi nếu có request
        $soDe = $request->input('so_de');
        $loaiDe = $request->input('loai_de') ?? '';
        $loai_ky = $request->input('loai_ky') ?? 'cuoi_ky';
        $kyThiText = $loai_ky == 'giua_ky' ? 'Giữa kỳ' : 'Cuối kỳ';
        $dsDe = [];
        
        // Lấy dữ liệu câu hỏi từ request
        $dsCauHoi = $request->input('dsCauHoi') ?? [];
        if (!is_array($dsCauHoi)) {
            $dsCauHoi = [];
        }
        
        // Debug logging
        Log::info('TrichXuatDeThi downloadFull', [
            'id' => $id,
            'loaiDe' => $loaiDe,
            'loai_ky' => $loai_ky,
            'dsCauHoi_count' => count($dsCauHoi),
            'dsCauHoi_sample' => array_slice($dsCauHoi, 0, 2) // Chỉ log 2 câu đầu để tránh quá dài
        ]);
        
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
                $dsDe[] = $de;
            }
        }
        
        return Inertia::render('TBM/TrichXuatDeThi/Show', [
            'hocPhan' => $hocPhan,
            'chuongs' => $chuongs,
            'cdrs' => $cdrs,
            'giao' => $giao,
            'bang' => $bang,
            'id' => $id,
            'soDe' => $soDe,
            'dsDe' => $dsDe,
            'loaiDe' => $loaiDe,
            'loai_ky' => $loai_ky,
            'role' => $role
        ]);
    }

} 