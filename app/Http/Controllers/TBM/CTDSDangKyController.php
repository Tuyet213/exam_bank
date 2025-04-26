<?php

namespace App\Http\Controllers\TBM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CTDSDangKy;
use App\Models\DSDangKy;
use App\Models\HocPhan;
use App\Models\User;
use App\Models\DSGVBienSoan;
use Inertia\Inertia;
use App\Imports\CTDSDangKyImport;
use Maatwebsite\Excel\Facades\Excel;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyTBMReview;
use Illuminate\Support\Facades\Log;
use App\Models\BienBanHop;
use Illuminate\Support\Facades\DB;

class CTDSDangKyController extends Controller
{
    public function index($id)
    {
        $can_create = True;
        $count = 0;
        $dsdangky = DSDangKy::with(['boMon'])->findOrFail($id);
        
        // Lấy danh sách chi tiết đăng ký với viên chức biên soạn
        $chitiet = CTDSDangKy::with(['hocPhan', 'dsGVBienSoans.vienChuc'])
            ->where('id_ds_dang_ky', $id)
            ->where('able', true)
            ->get()
            ->map(function ($ct) use (&$count) {
                if ($ct->trang_thai == 'Draft') {
                    $count++;
                }
                
                // Lấy danh sách viên chức biên soạn cho chi tiết này
                $vienChucs = $ct->dsGVBienSoans->map(function($gvbs) {
                    return $gvbs->vienChuc->name;
                })->join(', ');
                
                return [
                    'id' => $ct->id,
                    'ten' => $ct->ten,
                    'hoc_phan' => $ct->hocPhan->ten,
                    'vien_chuc' => $vienChucs,
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

        try {
            DB::beginTransaction();
            
            // Tạo chi tiết đăng ký
            $ctDSDangKy = CTDSDangKy::create([
                'id_ds_dang_ky' => $request->id_ds_dang_ky,
                'id_hoc_phan' => $request->id_hoc_phan,
                'hinh_thuc_thi' => $request->hinh_thuc_thi,
                'so_luong' => $request->so_luong,
                'trang_thai' => 'Draft',
                'able' => true,
                'so_gio' => 0,
            ]);
            
            // Tạo bản ghi viên chức biên soạn
            DSGVBienSoan::create([
                'id_ct_ds_dang_ky' => $ctDSDangKy->id,
                'id_vien_chuc' => $request->id_vien_chuc
            ]);
            
            DB::commit();
            
            return redirect()->route('tbm.ctdsdangky.index', $request->id_ds_dang_ky);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $ctdsdangky = CTDSDangKy::with(['hocPhan', 'dsGVBienSoans.vienChuc'])->findOrFail($id);
        $dsdangky = DSDangKy::findOrFail($ctdsdangky->id_ds_dang_ky);
        $nam_hoc = $dsdangky->nam_hoc;
        $hocphans = HocPhan::where('able', true)->where('id_bo_mon', $dsdangky->id_bo_mon)->get();
        $vienchucs = User::where('able', true)->where('id_bo_mon', $dsdangky->id_bo_mon)->get();
        $hinh_thuc_thi = $ctdsdangky->hinh_thuc_thi;
        
        // Lấy danh sách ID của tất cả viên chức đã được phân công
        $id_vien_chucs = $ctdsdangky->dsGVBienSoans->pluck('id_vien_chuc')->toArray();
        
        // Thêm thông tin danh sách viên chức vào đối tượng ctdsdangky để form có thể sử dụng
        $ctdsdangky->id_vien_chucs = $id_vien_chucs;
        
        // Giữ lại trường id_vien_chuc cho tương thích ngược
        $ctdsdangky->id_vien_chuc = count($id_vien_chucs) > 0 ? $id_vien_chucs[0] : null;
        
        return Inertia::render('TBM/CTDSDangKy/Edit', compact('ctdsdangky', 'hocphans', 'vienchucs', 'nam_hoc', 'dsdangky', 'hinh_thuc_thi'));
    }

    public function update(Request $request, $id)
    {
        Log::info($request->all());
        $validated = $request->validate([
            'id_hoc_phan' => 'required|exists:hoc_phans,id',
            'id_vien_chuc' => 'required|exists:users,id',
            'hinh_thuc_thi' => 'required|in:Trắc nghiệm,Tự luận,Trắc nghiệm và tự luận',
            'so_luong' => 'required|numeric|min:1',
            'loai_ngan_hang' => 'sometimes|in:0,1'
        ]);
        
        try {
            DB::beginTransaction();
            
            $ctdsdangky = CTDSDangKy::findOrFail($id);
            $ctdsdangky->update([
                'id_hoc_phan' => $request->id_hoc_phan,
                'hinh_thuc_thi' => $request->hinh_thuc_thi,
                'so_luong' => $request->so_luong,
                'loai_ngan_hang' => $request->loai_ngan_hang ?? $ctdsdangky->loai_ngan_hang,
                'so_gio' => 0,
            ]);
            
            // Cập nhật viên chức
            // Xóa tất cả bản ghi viên chức cũ
            DSGVBienSoan::where('id_ct_ds_dang_ky', $id)->delete();
            
            // Thêm bản ghi viên chức mới
            if (is_array($request->id_vien_chuc)) {
                // Xử lý khi id_vien_chuc là mảng
                foreach ($request->id_vien_chuc as $id_vien_chuc) {
                    DSGVBienSoan::create([
                        'id_ct_ds_dang_ky' => $id,
                        'id_vien_chuc' => $id_vien_chuc
                    ]);
                }
            } else {
                // Xử lý khi id_vien_chuc là giá trị đơn
                DSGVBienSoan::create([
                    'id_ct_ds_dang_ky' => $id,
                    'id_vien_chuc' => $request->id_vien_chuc
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('tbm.ctdsdangky.index', $ctdsdangky->id_ds_dang_ky);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật CTDSDangKy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // Xóa tất cả bản ghi liên quan trong DSGVBienSoan
            DSGVBienSoan::where('id_ct_ds_dang_ky', $id)->delete();
            
            // Xóa chi tiết đăng ký
            $ctdsdangky = CTDSDangKy::findOrFail($id);
            $id_ds_dang_ky = $ctdsdangky->id_ds_dang_ky;
            $ctdsdangky->delete();
            
            DB::commit();
            
            return redirect()->route('tbm.ctdsdangky.index', $id_ds_dang_ky);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
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