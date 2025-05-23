<?php

namespace App\Http\Controllers\QualityOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\BienBanHop;
use App\Models\Khoa;
use App\Models\BoMon;
use App\Models\VienChuc;
use App\Models\NamHoc;
use App\Models\HocKi;
use App\Models\CTDSDangKy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\NotifyApprovedBienBan;
use App\Notifications\NotifyRejectedBienBan;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class DSBienBanController extends Controller
{
    public function index(Request $request)
    {
        // Lọc dữ liệu theo request
        $query = BienBanHop::with([
            'ctDSDangKy', 
            'ctDSDangKy.hocPhan', 
            'ctDSDangKy.hocPhan.boMon', 
            'ctDSDangKy.dsDangKy',
            'ctDSDangKy.dsGVBienSoans.vienChuc'
        ]);
        
        // Tìm kiếm theo từ khóa
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('ctDSDangKy.hocPhan.boMon', function($q) use ($search) {
                $q->where('ten', 'like', "%{$search}%");
            })
            ->orWhereHas('ctDSDangKy.dsDangKy', function($q) use ($search) {
                $q->where('nam_hoc', 'like', "%{$search}%")
                  ->orWhere('hoc_ki', 'like', "%{$search}%");
            });
        }
        
        // Lọc theo khoa
        if ($request->has('khoa') && !empty($request->khoa)) {
            $query->whereHas('ctDSDangKy.hocPhan.boMon.khoa', function($q) use ($request) {
                $q->where('ten', $request->khoa);
            });
        }
        
        // Lọc theo bộ môn
        if ($request->has('bo_mon') && !empty($request->bo_mon)) {
            $query->whereHas('ctDSDangKy.hocPhan.boMon', function($q) use ($request) {
                $q->where('ten', $request->bo_mon);
            });
        }
        
        // Lọc theo học kỳ
        if ($request->has('hoc_ki') && !empty($request->hoc_ki)) {
            $query->whereHas('ctDSDangKy.dsDangKy', function($q) use ($request) {
                $q->where('hoc_ki', $request->hoc_ki);
            });
        }
        
        // Lọc theo năm học
        if ($request->has('nam_hoc') && !empty($request->nam_hoc)) {
            $query->whereHas('ctDSDangKy.dsDangKy', function($q) use ($request) {
                $q->where('nam_hoc', $request->nam_hoc);
            });
        }

        // Sắp xếp theo năm học và học kỳ từ bảng d_s_dang_kies
        $danhSachBienBan = $query->join('c_t_d_s_dang_kies', 'bien_ban_hops.id_ct_ds_dang_ky', '=', 'c_t_d_s_dang_kies.id')
            ->join('d_s_dang_kies', 'c_t_d_s_dang_kies.id_ds_dang_ky', '=', 'd_s_dang_kies.id')
            ->orderBy('d_s_dang_kies.nam_hoc', 'desc')
            ->orderBy('d_s_dang_kies.hoc_ki', 'asc')
            ->select('bien_ban_hops.*')
            ->get();
        
        // Xử lý trạng thái cho từng biên bản
        // $danhSachBienBan->each(function ($bb) {
        //     if (!$bb->noi_dung) {
        //         $bb->trang_thai = 'Chưa có file';
        //     } 
        //     else{

        //     }
        // });

        // Tổ chức dữ liệu theo cấu trúc phân cấp: năm học -> học kỳ -> khoa -> bộ môn -> danh sách biên bản
        $danhSachHierarchy = [];
        
        foreach ($danhSachBienBan as $bienBan) {
            $namHoc = $bienBan->ctDSDangKy->dsDangKy->nam_hoc;
            $hocKi = $bienBan->ctDSDangKy->dsDangKy->hoc_ki;
            $khoaTen = $bienBan->ctDSDangKy->hocPhan->boMon->khoa->ten;
            $khoaId = $bienBan->ctDSDangKy->hocPhan->boMon->khoa->id;
            $boMonTen = $bienBan->ctDSDangKy->hocPhan->boMon->ten;
            $boMonId = $bienBan->ctDSDangKy->hocPhan->boMon->id;
            
            // Tạo năm học nếu chưa tồn tại
            if (!isset($danhSachHierarchy[$namHoc])) {
                $danhSachHierarchy[$namHoc] = [
                    'ten' => $namHoc,
                    'hoc_ki' => []
                ];
            }
            
            // Tạo học kỳ nếu chưa tồn tại
            if (!isset($danhSachHierarchy[$namHoc]['hoc_ki'][$hocKi])) {
                $danhSachHierarchy[$namHoc]['hoc_ki'][$hocKi] = [
                    'ten' => 'Học kỳ ' . $hocKi,
                    'khoa' => []
                ];
            }
            
            // Tạo khoa nếu chưa tồn tại
            if (!isset($danhSachHierarchy[$namHoc]['hoc_ki'][$hocKi]['khoa'][$khoaId])) {
                $danhSachHierarchy[$namHoc]['hoc_ki'][$hocKi]['khoa'][$khoaId] = [
                    'ten' => $khoaTen,
                    'id' => $khoaId,
                    'bo_mon' => []
                ];
            }
            
            // Tạo bộ môn nếu chưa tồn tại
            if (!isset($danhSachHierarchy[$namHoc]['hoc_ki'][$hocKi]['khoa'][$khoaId]['bo_mon'][$boMonId])) {
                $danhSachHierarchy[$namHoc]['hoc_ki'][$hocKi]['khoa'][$khoaId]['bo_mon'][$boMonId] = [
                    'ten' => $boMonTen,
                    'id' => $boMonId,
                    'danh_sach' => []
                ];
            }
            
            // Thêm biên bản vào bộ môn tương ứng
            $danhSachHierarchy[$namHoc]['hoc_ki'][$hocKi]['khoa'][$khoaId]['bo_mon'][$boMonId]['danh_sach'][] = $bienBan;
        }
        
        // Sắp xếp theo năm học mới nhất trước
        krsort($danhSachHierarchy);
        
        // Lấy danh sách khoa (loại trừ admin và DBCL)
        $khoas = Khoa::whereNotIn('id', ['admin', 'DBCL'])
            ->orderBy('ten')
            ->get();
        
        // Danh sách tên khoa
        $dsKhoa = $khoas->pluck('ten')->toArray();
        
        // Lấy danh sách bộ môn (loại trừ admin và dbcl)
        // Đảm bảo chỉ lấy bộ môn thuộc các khoa hợp lệ
        $boMons = BoMon::with('khoa')
            ->whereNotIn('id', ['admin', 'dbcl'])
            ->whereHas('khoa', function($query) {
                $query->whereNotIn('id', ['admin', 'DBCL']);
            })
            ->orderBy('ten')
            ->get();
            
        // Danh sách tên bộ môn
        $dsBoMon = $boMons->pluck('ten')->toArray();

        // Tạo danh sách bộ môn theo khoa
        $boMonTheoKhoa = [];
        foreach ($khoas as $khoa) {
            $boMonTheoKhoa[$khoa->ten] = $boMons
                ->where('id_khoa', $khoa->id)
                ->pluck('ten')
                ->toArray();
        }
        
        // Lấy danh sách học kỳ (1, 2, 3)
        $dsHocKi = ['1', '2', 'Hè'];
        
        // Lấy danh sách năm học (từ năm hiện tại - 5 đến năm hiện tại + 1)
        $currentYear = now()->year;
        $dsNamHoc = [];
        for ($i = $currentYear - 5; $i <= $currentYear; $i++) {
            $dsNamHoc[] = $i . '-' . ($i + 1);
        }
        $dsNamHoc = array_reverse($dsNamHoc);

        return Inertia::render('QualityOffice/DSBienBan/Index', [
            'danhsachs_hierarchy' => $danhSachHierarchy,
            'ds_khoa' => $dsKhoa,
            'ds_bo_mon' => $dsBoMon,
            'bo_mon_theo_khoa' => $boMonTheoKhoa,
            'ds_hoc_ki' => $dsHocKi,
            'ds_nam_hoc' => $dsNamHoc,
            'filters' => $request->only(['search', 'khoa', 'bo_mon', 'hoc_ki', 'nam_hoc']),
        ]);
    }
    
    public function approve(Request $request, BienBanHop $bienban)
    {
        $bienban->trang_thai = 'Approved';
        $bienban->save();
        
        return redirect()->back()->with('success', 'Biên bản đã được duyệt thành công.');
    }
    
    public function reject(Request $request, BienBanHop $bienban)
    {
        $bienban->trang_thai = 'Rejected';
        $bienban->save();
        
        return redirect()->back()->with('success', 'Biên bản đã bị từ chối.');
    }
    
    public function download($id)
    {
        try {
            $bienBan = BienBanHop::findOrFail($id);
            
            if (!$bienBan->noi_dung) {
                return back()->with([
                    'type' => 'error',
                    'message' => 'Không tìm thấy file biên bản!'
                ]);
            }

            $filePath = public_path($bienBan->noi_dung);
            
            if (!file_exists($filePath)) {
                return back()->with([
                    'type' => 'error',
                    'message' => 'File biên bản không tồn tại!'
                ]);
            }

            return response()->download($filePath);
        } catch (\Exception $e) {
            Log::error('Lỗi tải xuống file:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
           
        }
    }

    public function show(BienBanHop $bienban)
    {
        // Eager load các mối quan hệ cần thiết
        $bienban->load([
            'ctDSDangKy',
            'ctDSDangKy.hocPhan',
            'ctDSDangKy.dsGVBienSoans.vienChuc',
            'ctDSDangKy.dsDangKy',
            'ctDSDangKy.hocPhan.boMon',
            'ctDSDangKy.hocPhan.boMon.khoa',
            'dsHop',
            'dsHop.vienChuc',
            'dsHop.nhiemVu'
        ]);

        return Inertia::render('QualityOffice/DSBienBan/Show', [
            'bienban' => $bienban,
            'ctDSDangKy' => $bienban->ctDSDangKy,
            'hocPhan' => $bienban->ctDSDangKy->hocPhan,
            'boMon' => $bienban->ctDSDangKy->hocPhan->boMon,
            'khoa' => $bienban->ctDSDangKy->hocPhan->boMon->khoa,
            'giangViens' => $bienban->ctDSDangKy->dsGVBienSoans,
            'thanhViens' => $bienban->dsHop
        ]);
    }

    public function approveWithEmail(Request $request, BienBanHop $bienban)
    {
        try {
            // Cập nhật trạng thái biên bản
            $bienban->trang_thai = 'Approved';
            $bienban->save();
            Log::info('Biên bản đã được duyệt');
            
            $bienban->load('ctDSDangKy');
            $ctDSDangKy = $bienban->ctDSDangKy;
            if ($ctDSDangKy) {
                $ctDSDangKy->trang_thai = 'Completed';
                $ctDSDangKy->save();
                Log::info('Đã cập nhật ctDSDangKy thành Completed');
            }
            
            // Lấy thông tin bộ môn từ biên bản
            $bienban->load('ctDSDangKy.hocPhan.boMon');
            $boMon = $bienban->ctDSDangKy->hocPhan->boMon;
            
            if (!$boMon) {
                return redirect()->back()->with('error', 'Không tìm thấy thông tin bộ môn.');
            }
            
            // Tìm Trưởng Bộ Môn
            $truongBoMon = User::where('id_bo_mon', $boMon->id)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'Trưởng Bộ Môn');
                })
                ->where('able', 1)
                ->first();
                
            if (!$truongBoMon || !$truongBoMon->email) {
                return redirect()->back()->with('success', 'Biên bản đã được duyệt nhưng không thể gửi email (không tìm thấy email Trưởng Bộ Môn).');
            }
            
            // Gửi email thông báo cho Trưởng Bộ Môn
            Mail::to($truongBoMon->email)->send(
                new \App\Mail\NotifyApprovedBienBan($bienban, $truongBoMon)
            );
            
            return redirect()->back()->with('success', 'Biên bản đã được duyệt và email thông báo đã được gửi đến Trưởng Bộ Môn.');
            
        } catch (\Exception $e) {
            Log::error('Lỗi khi duyệt biên bản: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi duyệt biên bản: ' . $e->getMessage());
        }
    }
    
    public function rejectWithEmail(Request $request, BienBanHop $bienban)
    {
        $request->validate([
            'ly_do' => 'required|string|min:3'
        ], [
            'ly_do.required' => 'Vui lòng nhập lý do từ chối biên bản',
            'ly_do.min' => 'Lý do từ chối phải có ít nhất 3 ký tự'
        ]);
        
        try {
            // Cập nhật trạng thái biên bản
            $bienban->trang_thai = 'Rejected';
            $bienban->save();
            
            // Lấy thông tin bộ môn từ biên bản
            $bienban->load('ctDSDangKy.hocPhan.boMon');
            $boMon = $bienban->ctDSDangKy->hocPhan->boMon;
            
            if (!$boMon) {
                return redirect()->back()->with('error', 'Không tìm thấy thông tin bộ môn.');
            }
            
            // Tìm Trưởng Bộ Môn
            $truongBoMon = User::where('id_bo_mon', $boMon->id)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'Trưởng Bộ Môn');
                })
                ->where('able', 1)
                ->first();
                
            if (!$truongBoMon || !$truongBoMon->email) {
                return redirect()->back()->with('success', 'Biên bản đã bị từ chối nhưng không thể gửi email (không tìm thấy email Trưởng Bộ Môn).');
            }
            
            // Gửi email thông báo cho Trưởng Bộ Môn
            Mail::to($truongBoMon->email)->send(
                new \App\Mail\NotifyRejectedBienBan($bienban, $truongBoMon, $request->ly_do)
            );
            
            return redirect()->back()->with('success', 'Biên bản đã bị từ chối và email thông báo đã được gửi đến Trưởng Bộ Môn.');
            
        } catch (\Exception $e) {
            Log::error('Lỗi khi từ chối biên bản: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi từ chối biên bản: ' . $e->getMessage());
        }
    }
} 