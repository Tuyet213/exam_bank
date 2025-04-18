<?php

namespace App\Http\Controllers\TBM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CTDSDangKy;
use App\Models\DSDangKy;
use App\Models\HocPhan;
use App\Models\User;
use Inertia\Inertia;
use App\Imports\CTDSDangKyImport;
use Maatwebsite\Excel\Facades\Excel;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyTBMReview;
use Illuminate\Support\Facades\Log;
use App\Models\BienBanHop;

class CTDSDangKyController extends Controller
{
    public function index($id)
    {
        $can_create = True;
        $count = 0;
        $dsdangky = DSDangKy::with(['boMon'])->findOrFail($id);
        $chitiet = CTDSDangKy::with(['hocPhan', 'vienChuc'])
            ->where('id_ds_dang_ky', $id)
            ->where('able', true)
            ->get()
            ->map(function ($ct) use (&$count) {
                if ($ct->trang_thai == 'Draft') {
                    $count++;
                }
                return [
                    'id' => $ct->id,
                    'ten' => $ct->ten,
                    'hoc_phan' => $ct->hocPhan->ten,
                    'vien_chuc' => $ct->vienChuc->name,
                    'so_gio' => $ct->so_gio,
                    'so_luong' => $ct->so_luong,
                    'loai_ngan_hang' => $ct->loai_ngan_hang==1?'Ngân hàng câu hỏi':'Ngân hàng đề thi',
                    'hinh_thuc_thi' => $ct->hinh_thuc_thi,
                    'trang_thai' => $ct->trang_thai
                ];
            });

        // Lấy danh sách ID của các chi tiết đăng ký đã có biên bản họp
        $ct_da_co_bien_ban = BienBanHop::where('able', true)
            ->whereIn('id_ct_ds_dang_ky', $chitiet->pluck('id'))
            ->pluck('id_ct_ds_dang_ky')
            ->toArray();

        $hocphans = HocPhan::where('able', true)->get();
        $vienchucs = User::where('able', true)->get();
        if ($count >= 1) {
            $can_create = False;
        }
        $hoc_ki = $dsdangky->hoc_ki;
        $nam_hoc = $dsdangky->nam_hoc;
        return Inertia::render('TBM/CTDSDangKy/Index', 
            compact('dsdangky', 'chitiet', 'hocphans', 'vienchucs', 'can_create', 'hoc_ki', 'nam_hoc', 'ct_da_co_bien_ban'));
    }
    public function create($id)
    {
        $dsdangky = DSDangKy::with(['boMon'])->findOrFail($id);
        $hocphans = HocPhan::where('able', true)->where('id_bo_mon', $dsdangky->id_bo_mon)->get();
        $vienchucs = User::where('able', true)->where('id_bo_mon', $dsdangky->id_bo_mon)->get();
        return Inertia::render('TBM/CTDSDangKy/Create', compact('dsdangky', 'hocphans', 'vienchucs'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'id_ds_dang_ky' => 'required|exists:d_s_dang_kies,id',
            'id_hoc_phan' => 'required|exists:hoc_phans,id',
            'id_vien_chuc' => 'required|exists:users,id',
            'hinh_thuc_thi' => 'required|in:Trắc nghiệm,Tự luận,Trắc nghiệm và tự luận',
            'so_luong' => 'required|numeric|min:1'
        ]);

        CTDSDangKy::create([
            'id_ds_dang_ky' => $request->id_ds_dang_ky,
            'id_hoc_phan' => $request->id_hoc_phan,
            'id_vien_chuc' => $request->id_vien_chuc,
            'hinh_thuc_thi' => $request->hinh_thuc_thi,
            'so_luong' => $request->so_luong,
            'trang_thai' => 'Draft',
            'able' => true,
            'so_gio' => 0,
        ]);

        return redirect()->route('tbm.ctdsdangky.index', $request->id_ds_dang_ky);
    }

    public function edit($id)
    {
        
        $ctdsdangky = CTDSDangKy::with(['hocPhan', 'vienChuc'])->findOrFail($id);
        $dsdangky = DSDangKy::findOrFail($ctdsdangky->id_ds_dang_ky);
        $nam_hoc = $dsdangky->nam_hoc;
        $hocphans = HocPhan::where('able', true)->where('id_bo_mon', $dsdangky->id_bo_mon)->get();
        $vienchucs = User::where('able', true)->where('id_bo_mon', $dsdangky->id_bo_mon)->get();
        $hinh_thuc_thi = $ctdsdangky->hinh_thuc_thi;
        return Inertia::render('TBM/CTDSDangKy/Edit', compact('ctdsdangky', 'hocphans', 'vienchucs', 'nam_hoc', 'dsdangky', 'hinh_thuc_thi'));
    }

    public function update(Request $request, $id)
    {
        Log::info($request->all());
        $validated = $request->validate([
            'id_hoc_phan' => 'required|exists:hoc_phans,id',
            'id_vien_chuc' => 'required|exists:users,id',
            'hinh_thuc_thi' => 'required|in:Trắc nghiệm,Tự luận,Trắc nghiệm và tự luận',
            'so_luong' => 'required|numeric|min:1'
        ]);
        Log::info($validated);
        $ctdsdangky = CTDSDangKy::findOrFail($id);
        $ctdsdangky->update([
            'id_hoc_phan' => $request->id_hoc_phan,
            'id_vien_chuc' => $request->id_vien_chuc,
            'hinh_thuc_thi' => $request->hinh_thuc_thi,
            'so_luong' => $request->so_luong,
            'so_gio' => 0,
        ]);
        Log::info($ctdsdangky);
        return redirect()->route('tbm.ctdsdangky.index', $ctdsdangky->id_ds_dang_ky);
    }

    public function destroy($id)
    {
        $ctdsdangky = CTDSDangKy::findOrFail($id);
        $ctdsdangky->delete();
        return redirect()->route('tbm.ctdsdangky.index', $ctdsdangky->id_ds_dang_ky);
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $dsDangKy = DSDangKy::findOrFail($request->id_ds_dang_ky);
            
            Excel::import(
                new CTDSDangKyImport($request->id_ds_dang_ky, $dsDangKy->id_bo_mon),
                $request->file('file')
            );

            return redirect()->back()->with([
                'success' => 'Import dữ liệu thành công!',
                'error' => null
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'success' => null,
                'error' => 'Có lỗi xảy ra khi import: ' . $e->getMessage()
            ]);
        }
    }

    
}