<?php

namespace App\Http\Controllers\TK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DSDangKy;
use App\Models\CTDSDangKy;
use App\Models\BoMon;
use App\Models\HocPhan;
use App\Models\BienBanHop;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\DSHop;
use App\Models\User;
use App\Models\NhiemVu;

class DSDangKyKhoaController extends Controller
{
    /**
     * Hiển thị danh sách đăng ký của khoa
     */
    public function index(Request $request)
    {
        // Lấy id_khoa từ bộ môn của user
        $user = Auth::user();
        $boMon = BoMon::find($user->id_bo_mon);
        $idKhoa = $boMon ? $boMon->id_khoa : null;

        Log::info('User info:', [
            'id' => $user->id,
            'name' => $user->name,
            'id_bo_mon' => $user->id_bo_mon,
            'id_khoa' => $idKhoa
        ]);

        $query = DSDangKy::with([
            'boMon',
            'boMon.khoa',
        ])
        ->join('bo_mons', 'd_s_dang_kies.id_bo_mon', '=', 'bo_mons.id')
        ->where('bo_mons.id_khoa', $idKhoa)
        ->where('d_s_dang_kies.able', true)
        ->orderBy('d_s_dang_kies.created_at', 'desc')
        ->select('d_s_dang_kies.*');

        // Lọc theo bộ môn nếu có
        if ($request->has('bo_mon') && $request->bo_mon != '') {
            $query->where('d_s_dang_kies.id_bo_mon', $request->bo_mon);
        }

        // Lọc theo học kỳ nếu có
        if ($request->has('hoc_ki') && $request->hoc_ki != '') {
            $query->where('d_s_dang_kies.hoc_ki', $request->hoc_ki);
        }

        // Lọc theo năm học nếu có
        if ($request->has('nam_hoc') && $request->nam_hoc != '') {
            $query->where('d_s_dang_kies.nam_hoc', $request->nam_hoc);
        }
        $dsDangKy = $query->get();
        // Kiểm tra xem mỗi đăng ký đã có biên bản họp chưa
        foreach ($dsDangKy as $dangKy) {
            $dangKy->has_bien_ban = BienBanHop::where('id_ds_dang_ky', $dangKy->id)->exists();
        }
        
        $dsBoMon = BoMon::where('id_khoa', $idKhoa)
            ->where('able', true)
            ->get();

        // Lấy danh sách học kỳ và năm học để hiển thị trong select
        $dsHocKi = DSDangKy::select('hoc_ki')
            ->join('bo_mons', 'd_s_dang_kies.id_bo_mon', '=', 'bo_mons.id')
            ->where('bo_mons.id_khoa', $idKhoa)
            ->distinct()
            ->orderBy('hoc_ki')
            ->pluck('hoc_ki');

        $dsNamHoc = DSDangKy::select('nam_hoc')
            ->join('bo_mons', 'd_s_dang_kies.id_bo_mon', '=', 'bo_mons.id')
            ->where('bo_mons.id_khoa', $idKhoa)
            ->distinct()
            ->orderBy('nam_hoc', 'desc')
            ->pluck('nam_hoc');

        return Inertia::render('TK/DSDangKyKhoa/Index', [
            'ds_dang_ky' => $dsDangKy,
            'ds_bo_mon' => $dsBoMon,
            'ds_hoc_ki' => $dsHocKi,
            'ds_nam_hoc' => $dsNamHoc,
            'filters' => [
                'bo_mon' => $request->bo_mon,
                'hoc_ki' => $request->hoc_ki,
                'nam_hoc' => $request->nam_hoc
            ]
        ]);
    }

    
} 