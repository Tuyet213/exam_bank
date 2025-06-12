<?php

namespace App\Http\Controllers;

use App\Models\DeThi;
use App\Models\CTDSDangKy;
use App\Models\HocPhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DeThiController extends Controller
{
    public function danhSachHocPhan(Request $request)
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
        
        $query = CTDSDangKy::whereHas('dsGvBienSoans', function($query) use ($user) {
                $query->where('id_vien_chuc', $user->id)->where('able', true);
            })
            ->where('loai_ngan_hang', 0)->where('able', true) // Ngân hàng đề thi
            ->whereIn('trang_thai', ['Approved', 'Completed']) // Chỉ hiển thị những CTDSDangKy đã được duyệt hoặc hoàn thành
            ->with(['hocPhan', 'dsDangKy', 'dsGvBienSoans']);

        // Lọc theo tên/mã học phần
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('hocPhan', function($q) use ($search) {
                $q->where('ten', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->where('able', true);
            });
        }

        // Lọc theo năm học
        if ($request->filled('nam_hoc')) {
            $query->whereHas('dsDangKy', function($q) use ($request) {
                $q->where('nam_hoc', $request->nam_hoc)
                  ->where('able', true);
            });
        }

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky')) {
            $query->whereHas('dsDangKy', function($q) use ($request) {
                $q->where('hoc_ki', $request->hoc_ky)
                  ->where('able', true);
            });
        }

        // Lọc theo hình thức thi
        if ($request->filled('hinh_thuc_thi')) {
            $query->where('hinh_thuc_thi', $request->hinh_thuc_thi);
        }

        $ctdsdangkies = $query->get();

        return Inertia::render('DeThi/DanhSachHocPhan', [
            'ctdsdangkies' => $ctdsdangkies,
            'role' => $role,
            'filters' => $request->only(['search', 'nam_hoc', 'hoc_ky', 'hinh_thuc_thi'])
        ]);
    }

    public function danhSachDeThi($id, Request $request)
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
        
        $ctDangKy = CTDSDangKy::with(['hocPhan', 'dsDangKy'])->where('able', true)->findOrFail($id);
        $query = DeThi::with(['hocPhan', 'ctDSDangKy'])->where('id_ct_ds_dang_ky', $id);

        // Lọc theo loại đề thi
        if ($request->filled('loai')) {
            // Convert string từ Vue sang number cho database
            $loaiNumber = DeThi::convertHinhThucThiToLoai($request->loai);
            $query->where('loai', $loaiNumber);
        }

        // Lọc theo ngày tạo (từ ngày)
        if ($request->filled('tu_ngay')) {
            $query->whereDate('created_at', '>=', $request->tu_ngay);
        }

        // Lọc theo ngày tạo (đến ngày)
        if ($request->filled('den_ngay')) {
            $query->whereDate('created_at', '<=', $request->den_ngay);
        }

        $deThis = $query->orderBy('created_at', 'desc')->get();

        return Inertia::render('DeThi/DanhSach', [
            'ctDangKy' => $ctDangKy,
            'deThis' => $deThis,
            'role' => $role,
            'filters' => $request->only(['loai', 'tu_ngay', 'den_ngay'])
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
        
        $ctDangKy = CTDSDangKy::with(['hocPhan', 'dsDangKy'])->where('able', true)->findOrFail($id);

        return Inertia::render('DeThi/Tao', [
            'ctDangKy' => $ctDangKy,
            'role' => $role
        ]);
    }

    public function luu(Request $request)
    {
        // Debug file information
        if ($request->hasFile('file_de')) {
            Log::info('File đề thi info', [
                'name' => $request->file('file_de')->getClientOriginalName(),
                'mime' => $request->file('file_de')->getMimeType(),
                'extension' => $request->file('file_de')->getClientOriginalExtension(),
                'size' => $request->file('file_de')->getSize()
            ]);
        }
        
        if ($request->hasFile('file_dap_an')) {
            Log::info('File đáp án info', [
                'name' => $request->file('file_dap_an')->getClientOriginalName(),
                'mime' => $request->file('file_dap_an')->getMimeType(),
                'extension' => $request->file('file_dap_an')->getClientOriginalExtension(),
                'size' => $request->file('file_dap_an')->getSize()
            ]);
        }

        // Validation rules - chỉ check extension, không check MIME type
        $validated = $request->validate([
            'id_ct_ds_dang_ky' => 'required|exists:c_t_d_s_dang_kies,id',
            'file_de' => 'required|file|max:10240', // Max 10MB, chỉ check size
            'file_dap_an' => 'nullable|file|max:10240', // Max 10MB, chỉ check size
        ], [
            'file_de.required' => 'Vui lòng chọn file đề thi',
            'file_de.file' => 'File đề thi không hợp lệ',
            'file_de.max' => 'File đề thi không được vượt quá 10MB',
            'file_dap_an.file' => 'File đáp án không hợp lệ',
            'file_dap_an.max' => 'File đáp án không được vượt quá 10MB',
        ]);

        // Manual validation cho extension
        $allowedExtensions = ['pdf', 'doc', 'docx'];
        
        if ($request->hasFile('file_de')) {
            $extension = strtolower($request->file('file_de')->getClientOriginalExtension());
            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()
                    ->withErrors(['file_de' => 'File đề thi phải có định dạng: PDF, DOC, DOCX'])
                    ->withInput();
            }
        }
        
        if ($request->hasFile('file_dap_an')) {
            $extension = strtolower($request->file('file_dap_an')->getClientOriginalExtension());
            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()
                    ->withErrors(['file_dap_an' => 'File đáp án phải có định dạng: PDF, DOC, DOCX'])
                    ->withInput();
            }
        }

        // Lấy thông tin CTDSDangKy để lấy thông tin học phần
        $ctDangKy = CTDSDangKy::with(['hocPhan', 'dsDangKy'])->findOrFail($request->id_ct_ds_dang_ky);

        DB::beginTransaction();
        try {
            // Lấy loại đề thi từ hình thức thi của CTDSDangKy
            $hinhThucThi = $ctDangKy->hinh_thuc_thi;
            $loaiDeThi = DeThi::convertHinhThucThiToLoai($hinhThucThi);
            
            // Tạo thư mục theo cấu trúc: de_thi/{ma_hoc_phan}/{loai}/
            $loaiFolder = strtolower(str_replace(['/', ' '], ['_', '_'], $hinhThucThi));
            $folderPath = 'de_thi/' . $ctDangKy->hocPhan->id . '/' . $loaiFolder . '/';

            // Lưu file đề thi
            $deFileName = 'de_' . time() . '_' . $request->file('file_de')->getClientOriginalName();
            $deFilePath = $request->file('file_de')->storeAs($folderPath, $deFileName, 'public');

            // Lưu file đáp án (nếu có)
            $dapAnFilePath = null;
            if ($request->hasFile('file_dap_an')) {
                $dapAnFileName = 'dap_an_' . time() . '_' . $request->file('file_dap_an')->getClientOriginalName();
                $dapAnFilePath = $request->file('file_dap_an')->storeAs($folderPath, $dapAnFileName, 'public');
            }

            // Tạo bản ghi đề thi với loại là số
            $deThi = DeThi::create([
                'de' => $deFilePath,
                'dap_an' => $dapAnFilePath,
                'loai' => $loaiDeThi, // Sử dụng số (0 hoặc 1)
                'id_hoc_phan' => $ctDangKy->hocPhan->id,
                'id_ct_ds_dang_ky' => $request->id_ct_ds_dang_ky
            ]);

            DB::commit();

            Log::info('Tạo đề thi thành công', [
                'de_thi_id' => $deThi->id,
                'hoc_phan_id' => $ctDangKy->hocPhan->id,
                'loai' => $loaiDeThi,
                'file_de' => $deFilePath,
                'file_dap_an' => $dapAnFilePath
            ]);

            return redirect()->route('dethi.danhsach', $request->id_ct_ds_dang_ky)
                ->with('success', 'Tạo đề thi thành công');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo đề thi: ' . $e->getMessage());
            
            // Xóa file đã upload nếu có lỗi
            if (isset($deFilePath) && Storage::disk('public')->exists($deFilePath)) {
                Storage::disk('public')->delete($deFilePath);
            }
            if (isset($dapAnFilePath) && Storage::disk('public')->exists($dapAnFilePath)) {
                Storage::disk('public')->delete($dapAnFilePath);
            }

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tạo đề thi: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function xoa($id)
    {
        $deThi = DeThi::findOrFail($id);
        $ctdsdangkyId = $deThi->id_ct_ds_dang_ky;
        
        // Xóa các file liên quan
        if ($deThi->de && Storage::disk('public')->exists($deThi->de)) {
            Storage::disk('public')->delete($deThi->de);
        }
        if ($deThi->dap_an && Storage::disk('public')->exists($deThi->dap_an)) {
            Storage::disk('public')->delete($deThi->dap_an);
        }

        // Xóa đề thi
        $deThi->delete();

        return redirect()->route('dethi.danhsach', $ctdsdangkyId)
            ->with('success', 'Xóa đề thi thành công');
    }

    public function downloadDe($id)
    {
        $deThi = DeThi::findOrFail($id);
        
        if (!$deThi->de || !Storage::disk('public')->exists($deThi->de)) {
            return redirect()->back()->with('error', 'File đề thi không tồn tại');
        }

        $filePath = storage_path('app/public/' . $deThi->de);
        
        // Lấy extension từ file gốc
        $originalExtension = pathinfo($deThi->de, PATHINFO_EXTENSION);
        
        // Tạo tên file với extension đúng
        $fileName = 'De_thi_' . $deThi->hocPhan->ten . '_' . ($deThi->loai == 0 ? 'Trac_nghiem' : 'Tu_luan') . '.' . $originalExtension;
        
        return response()->download($filePath, $fileName);
    }

    public function downloadDapAn($id)
    {
        $deThi = DeThi::findOrFail($id);
        
        if (!$deThi->dap_an || !Storage::disk('public')->exists($deThi->dap_an)) {
            return redirect()->back()->with('error', 'File đáp án không tồn tại');
        }

        $filePath = storage_path('app/public/' . $deThi->dap_an);
        
        // Lấy extension từ file gốc  
        $originalExtension = pathinfo($deThi->dap_an, PATHINFO_EXTENSION);
        
        // Tạo tên file với extension đúng
        $fileName = 'Dap_an_' . $deThi->hocPhan->ten . '_' . ($deThi->loai == 0 ? 'Trac_nghiem' : 'Tu_luan') . '.' . $originalExtension;
        
        return response()->download($filePath, $fileName);
    }
}
