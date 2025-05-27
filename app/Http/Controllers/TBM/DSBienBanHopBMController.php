<?php

namespace App\Http\Controllers\TBM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BienBanHop;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\DSDangKy;
use App\Models\CTDSDangKy;
use App\Models\HocPhan;
use App\Models\User;
use App\Models\DSHop;
use App\Models\NhiemVu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\GioQuyDoi;
use App\Models\BoMon;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyCompletedBienSoan;

class DSBienBanHopBMController extends Controller
{
    /**
     * Hiển thị danh sách biên bản họp bộ môn
     */
    public function index(Request $request)
    {
        $query = BienBanHop::with([
            'ctDSDangKy',
            'ctDSDangKy.dsDangKy.boMon',
            'ctDSDangKy.hocPhan',
            'ctDSDangKy.dsGVBienSoans.vienChuc'
        ])
        ->whereHas('ctDSDangKy.hocPhan', function($query) {
            $query->where('id_bo_mon', Auth::user()->id_bo_mon)
                  ->where('able', true);
        })
        ->where('bien_ban_hops.able', true)
        ->join('c_t_d_s_dang_kies', 'bien_ban_hops.id_ct_ds_dang_ky', '=', 'c_t_d_s_dang_kies.id')
        ->join('d_s_dang_kies', 'c_t_d_s_dang_kies.id_ds_dang_ky', '=', 'd_s_dang_kies.id')
        ->where('c_t_d_s_dang_kies.able', true)
        ->where('d_s_dang_kies.able', true)
        ->orderBy('d_s_dang_kies.created_at', 'desc')
        ->orderBy('d_s_dang_kies.id', 'desc')
        ->select('bien_ban_hops.*')->where('cap', 'Bộ môn');

        // Lọc theo học kỳ nếu có
        if ($request->has('hoc_ki') && $request->hoc_ki != '') {
            $query->whereHas('ctDSDangKy.dsDangKy', function($q) use ($request) {
                $q->where('hoc_ki', $request->hoc_ki)
                  ->where('able', true);
            });
        }

        // Lọc theo năm học nếu có
        if ($request->has('nam_hoc') && $request->nam_hoc != '') {
            $query->whereHas('ctDSDangKy.dsDangKy', function($q) use ($request) {
                $q->where('nam_hoc', $request->nam_hoc)
                  ->where('able', true);
            });
        }

        $dsBienBan = $query->orderBy('created_at', 'desc')->get();

        // Cố định danh sách học kỳ (1, 2, Hè)
        $dsHocKi = ['1', '2', 'Hè'];

        // Tạo danh sách năm học dài hơn
        $currentYear = date('Y');
        $dsNamHoc = [];
        for ($i = $currentYear - 5; $i <= $currentYear; $i++) {
            $dsNamHoc[] = $i . '-' . ($i + 1);
        }
        $dsNamHoc = array_reverse($dsNamHoc);
        
        // Tổ chức dữ liệu theo cấu trúc phân cấp: năm học -> học kỳ -> danh sách biên bản
        $bienBanHierarchy = [];
        
        foreach ($dsBienBan as $bienBan) {
            $namHoc = $bienBan->ctDSDangKy->dsDangKy->nam_hoc ?? 'Không xác định';
            $hocKi = $bienBan->ctDSDangKy->dsDangKy->hoc_ki ?? 'Không xác định';
            
            // Tạo năm học nếu chưa tồn tại
            if (!isset($bienBanHierarchy[$namHoc])) {
                $bienBanHierarchy[$namHoc] = [
                    'ten' => $namHoc,
                    'hoc_ki' => []
                ];
            }
            
            // Tạo học kỳ nếu chưa tồn tại
            if (!isset($bienBanHierarchy[$namHoc]['hoc_ki'][$hocKi])) {
                $bienBanHierarchy[$namHoc]['hoc_ki'][$hocKi] = [
                    'ten' => 'Học kỳ ' . $hocKi,
                    'danh_sach' => []
                ];
            }
            
            // Thêm biên bản vào học kỳ tương ứng
            $bienBanHierarchy[$namHoc]['hoc_ki'][$hocKi]['danh_sach'][] = $bienBan;
        }
        
        // Sắp xếp theo năm học mới nhất trước
        krsort($bienBanHierarchy);

        return Inertia::render('TBM/DSBienBanHopBM/Index', [
            'ds_bien_ban' => $dsBienBan,
            'ds_bien_ban_hierarchy' => $bienBanHierarchy,
            'ds_hoc_ki' => $dsHocKi,
            'ds_nam_hoc' => $dsNamHoc,
            'filters' => [
                'hoc_ki' => $request->hoc_ki,
                'nam_hoc' => $request->nam_hoc
            ]
        ]);
    }

    /**
     * Hiển thị form tạo biên bản họp
     */
    public function create(Request $request)
    {
        // Lấy danh sách CTDSDangKy được chọn
        $ctDSDangKyIds = $request->input('ct_ds_dang_ky_ids', []);
        
        // Lấy thông tin chi tiết
        $ctDSDangKies = CTDSDangKy::with(['hocPhan', 'dsGVBienSoans.vienChuc', 'dsDangKy'])
            ->whereIn('id', $ctDSDangKyIds)
            ->get();

        // Kiểm tra quyền truy cập
        foreach ($ctDSDangKies as $ct) {
            if ($ct->hocPhan->id_bo_mon !== Auth::user()->id_bo_mon) {
                return redirect()->back()->with('error', 'Bạn không có quyền tạo biên bản cho học phần này');
            }
        }

        // Lấy danh sách viên chức của bộ môn
        $vienChucs = User::where('id_bo_mon', Auth::user()->id_bo_mon)
            ->where('able', 1)
            ->get();
            
        // Lấy danh sách nhân viên P.ĐBCL
        $nhanVienDBCL = User::whereHas('roles', function($query) {
                $query->where('name', 'Nhân viên P.ĐBCL');
            })
            ->where('able', 1)
            ->get();

        // Lấy các nhiệm vụ cụ thể từ bảng NhiemVu
        $nhiemVus = NhiemVu::whereIn('ten', ['Chủ tịch', 'Thư ký', 'Cán bộ phản biện', 'Ủy viên'])
            ->where('able', 1)
            ->get();

        return Inertia::render('TBM/DSBienBanHopBM/Create', [
            'ct_ds_dang_kies' => $ctDSDangKies,
            'vien_chucs' => $vienChucs,
            'nhan_vien_dbcl' => $nhanVienDBCL,
            'nhiem_vus' => $nhiemVus
        ]);
    }

    /**
     * Lưu biên bản họp mới
     */
    public function store(Request $request)
    {
        try {
            // Log request data để debug
            Log::info('Request data:', $request->all());

            $validated = $request->validate([
                'id_ct_ds_dang_ky' => 'required|exists:c_t_d_s_dang_kies,id',
                'thoi_gian' => 'required|date_format:Y-m-d\TH:i',
                'dia_diem' => 'required|string',
                'ds_hop' => 'required|array|min:1',
                'ds_hop.*.id_vien_chuc' => 'required|exists:users,id',
                'ds_hop.*.id_nhiem_vu' => 'required|exists:nhiem_vus,id'
            ]);
            Log::info('Validated data:', $validated);

            DB::beginTransaction();

            // Tạo biên bản họp
            $bienBan = BienBanHop::create([
                'id_ct_ds_dang_ky' => $request->id_ct_ds_dang_ky,
                'noi_dung' => null,
                'thoi_gian' => $request->thoi_gian,
                'dia_diem' => $request->dia_diem,
                'cap' => 'Bộ môn',
                'able' => true,
                'trang_thai' => 'Draft'
            ]);
            Log::info('Biên bản họp đã được tạo:', $bienBan->toArray());

            // Lưu danh sách người tham gia
            foreach ($request->ds_hop as $thanhVien) {
                $dsThanhVien = $bienBan->dsHop()->create([
                    'id_bien_ban_hop' => $bienBan->id,
                    'id_vien_chuc' => $thanhVien['id_vien_chuc'],
                    'id_nhiem_vu' => $thanhVien['id_nhiem_vu'],
                    'able' => true
                ]);
                Log::info('Đã thêm thành viên:', $dsThanhVien->toArray());
            }

            DB::commit();
            Log::info('Đã commit transaction thành công');
            
            return back()->with([
                'type' => 'success',
                'message' => 'Tạo biên bản họp thành công!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error:', [
                'errors' => $e->errors(),
                'data' => $request->all()
            ]);
            return back()->withErrors($e->errors())->with([
                'type' => 'error',
                'message' => 'Vui lòng kiểm tra lại thông tin!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo biên bản họp:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with([
                'type' => 'error',
                'message' => 'Có lỗi xảy ra khi tạo biên bản họp: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Hiển thị chi tiết biên bản
     */
    public function show($id)
    {
        $bienBan = BienBanHop::with(['ctDSDangKy.hocPhan.boMon'])
            ->whereHas('ctDSDangKy.hocPhan', function($query) {
                $query->where('id_bo_mon', Auth::user()->id_bo_mon);
            })
            ->findOrFail($id);

        return Inertia::render('TBM/DSBienBanHopBM/Show', [
            'bien_ban' => $bienBan
        ]);
    }

    /**
     * Hiển thị form chỉnh sửa biên bản
     */
    public function edit($id)
    {
        $bienBan = BienBanHop::with(['ctDSDangKy.hocPhan', 'ctDSDangKy.dsGVBienSoans.vienChuc', 'ctDSDangKy.dsDangKy', 'dsHop'])
            ->findOrFail($id);

        // Kiểm tra quyền truy cập
        if ($bienBan->ctDSDangKy->hocPhan->id_bo_mon !== Auth::user()->id_bo_mon) {
            return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa biên bản này');
        }

        // Lấy danh sách viên chức của bộ môn
        $vienChucs = User::where('id_bo_mon', Auth::user()->id_bo_mon)
            ->where('able', 1)
            ->get();
            
        // Lấy danh sách nhân viên P.ĐBCL
        $nhanVienDBCL = User::whereHas('roles', function($query) {
                $query->where('name', 'Nhân viên P.ĐBCL');
            })
            ->where('able', 1)
            ->get();

        // Lấy các nhiệm vụ cụ thể từ bảng NhiemVu
        $nhiemVus = NhiemVu::whereIn('ten', ['Chủ tịch', 'Thư ký', 'Cán bộ phản biện', 'Ủy viên'])
            ->where('able', 1)
            ->get();

        return Inertia::render('TBM/DSBienBanHopBM/Edit', [
            'bien_ban' => $bienBan,
            'vien_chucs' => $vienChucs,
            'nhan_vien_dbcl' => $nhanVienDBCL,
            'nhiem_vus' => $nhiemVus
        ]);
    }

    /**
     * Cập nhật biên bản họp
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Request data:', $request->all());

            $validated = $request->validate([
                'thoi_gian' => 'required|date_format:Y-m-d\TH:i',
                'dia_diem' => 'required|string|max:255',
                'ds_hop' => 'required|array|min:1',
                'ds_hop.*.id_vien_chuc' => 'required|exists:users,id',
                'ds_hop.*.id_nhiem_vu' => 'required|exists:nhiem_vus,id'
            ]);
            Log::info('Validated data:', $validated);

            DB::beginTransaction();

            // Cập nhật thông tin biên bản
            $bienBan = BienBanHop::with('ctDSDangKy')->findOrFail($id);
            $bienBan->update([
                'thoi_gian' => $request->thoi_gian,
                'dia_diem' => $request->dia_diem
            ]);

            Log::info('Đã cập nhật thông tin biên bản:', $bienBan->toArray());

            // Cập nhật thông tin người tham gia
            foreach ($request->ds_hop as $thanhVien) {
                if (isset($thanhVien['id'])) {
                    // Cập nhật thành viên đã tồn tại
                    $dsHop = DSHop::where('id', $thanhVien['id'])->first();
                    if ($dsHop) {
                        $dsHop->update([
                            'id_vien_chuc' => $thanhVien['id_vien_chuc'],
                            'id_nhiem_vu' => $thanhVien['id_nhiem_vu']
                        ]);
                        Log::info('Đã cập nhật thành viên:', $dsHop->toArray());
                    } else {
                        Log::warning('Không tìm thấy thành viên với ID:', ['id' => $thanhVien['id']]);
                    }
                } else {
                    Log::warning('Thiếu ID trong dữ liệu thành viên:', $thanhVien);
                }
            }

            DB::commit();
            Log::info('Đã commit transaction thành công');

            return back()->with([
                'type' => 'success',
                'message' => 'Cập nhật biên bản họp thành công!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error:', [
                'errors' => $e->errors(),
                'data' => $request->all()
            ]);
            return back()->withErrors($e->errors())->with([
                'type' => 'error',
                'message' => 'Vui lòng kiểm tra lại thông tin!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật biên bản:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with([
                'type' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Xóa biên bản
     */
    public function destroy($id)
    {
        try {
            $bienBan = BienBanHop::whereHas('ctDSDangKy.hocPhan', function($query) {
                    $query->where('id_bo_mon', Auth::user()->id_bo_mon);
                })
                ->findOrFail($id);

            $bienBan->delete();

            return redirect()->route('tbm.dsbienban.index')
                ->with('success', true)
                ->with('message', 'Xóa biên bản họp thành công!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('success', false)
                ->with('message', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function uploadNoiDung(Request $request, $id)
    {
        try {
            $request->validate([
                'noi_dung' => 'required|mimes:pdf|max:10240', // max 10MB
            ]);

            $bienBan = BienBanHop::with(['ctDSDangKy.hocPhan', 'ctDSDangKy.dsGVBienSoans.vienChuc', 'ctDSDangKy.dsDangKy'])
                ->findOrFail($id);

            // Tạo tên file theo format: hoc_phan_giang_vien_hoc_ki_nam.pdf
            $fileName = Str::slug(
                $bienBan->ctDSDangKy->hocPhan->ten . '_' . 
                $bienBan->ctDSDangKy->dsGVBienSoans->first()->vienChuc->name . '_' .
                $bienBan->ctDSDangKy->dsDangKy->hoc_ki . '_' .
                $bienBan->ctDSDangKy->dsDangKy->nam_hoc
            ) . '.pdf';

            // Lưu file vào storage
            $request->file('noi_dung')->move(
                public_path('storage/bien_ban_hop'),
                $fileName
            );

            // Cập nhật đường dẫn file trong database
            $bienBan->update([
                'noi_dung' => 'storage/bien_ban_hop/' . $fileName
            ]);

            return redirect()->route('tbm.dsbienban.index')->with('success', 'Upload nội dung thành công!');

        } catch (\Exception $e) {
            Log::error('Lỗi upload file:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('tbm.dsbienban.index')->with('error', 'Có lỗi xảy ra khi upload file: ' . $e->getMessage());
        }
    }

    public function editSoGio($id)
    {
        $truongBoMon = Auth::user();
        
        // Kiểm tra xem người dùng có phải là trưởng bộ môn không
        if (!$truongBoMon->id_bo_mon) {
            return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }
        
        $bienBan = BienBanHop::with(['dsHop.vienChuc', 'dsHop.nhiemVu', 'ctDSDangKy.dsGVBienSoans.vienChuc', 'ctDSDangKy.hocPhan'])
            ->where('id', $id)
            ->first();

        // Kiểm tra xem biên bản có thuộc bộ môn của trưởng bộ môn không
        if ($bienBan->ctDSDangKy->hocPhan->id_bo_mon != $truongBoMon->id_bo_mon) {
            return redirect()->route('tbm.dsbienban.index')->with('error', 'Biên bản không thuộc bộ môn của bạn');
        }

        // Lấy giờ quy đổi
        $gioQuyDoiBienSoan = GioQuyDoi::where('able', true)
            ->where('loai_hanh_dong', 0)
            ->get();
        
        $gioQuyDoiPhanBien = GioQuyDoi::where('able', true)
            ->where('loai_hanh_dong', 1)
            ->get();

        return Inertia::render('TBM/DSBienBanHopBM/EditSoGio', [
            'bien_ban' => $bienBan,
            'gio_quy_doi_bien_soan' => $gioQuyDoiBienSoan,
            'gio_quy_doi_phan_bien' => $gioQuyDoiPhanBien
        ]);
    }

    public function updateSoGio(Request $request, $id)
    {
        $truongBoMon = Auth::user();
        
        // Kiểm tra xem người dùng có phải là trưởng bộ môn không
        if (!$truongBoMon->id_bo_mon) {
            return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }
        
        $bienBan = BienBanHop::with(['dsHop', 'ctDSDangKy.hocPhan', 'ctDSDangKy.dsGVBienSoans'])
            ->where('id', $id)
            ->first();

        // Kiểm tra xem biên bản có thuộc bộ môn của trưởng bộ môn không
        if ($bienBan->ctDSDangKy->hocPhan->id_bo_mon != $truongBoMon->id_bo_mon) {
            return redirect()->route('tbm.dsbienban.index')->with('error', 'Biên bản không thuộc bộ môn của bạn');
        }

        // Validate request
        $validated = $request->validate([
            'ds_g_v_bien_soans' => 'required|array',
            'ds_g_v_bien_soans.*.id' => 'required|exists:d_s_g_v_bien_soans,id',
            'ds_g_v_bien_soans.*.so_gio' => 'required|numeric|min:0',
            'ds_hop.*.id' => 'required|exists:d_s_hops,id',
            'ds_hop.*.so_gio' => 'required|numeric|min:0',
            'id_gio_quy_doi_bien_soan' => 'nullable|exists:gio_quy_dois,id',
            'id_gio_quy_doi_phan_bien' => 'nullable|exists:gio_quy_dois,id',
        ]);

        // Kiểm tra tổng số giờ biên soạn nếu đã chọn giờ quy đổi biên soạn
        if ($request->id_gio_quy_doi_bien_soan) {
            $gioQuyDoiBienSoan = GioQuyDoi::find($request->id_gio_quy_doi_bien_soan);
            $soLuong = $bienBan->ctDSDangKy->so_luong;
            $gioQuyDoi = $gioQuyDoiBienSoan->gio;
            $soLuongCauQuyDoi = $gioQuyDoiBienSoan->so_luong;
            
            $tongSoGioDuKien = ($soLuong / $soLuongCauQuyDoi) * $gioQuyDoi;
            $tongSoGioThucTe = collect($request->ds_g_v_bien_soans)->sum('so_gio');
            
            // Cho phép sai số 0.1
            if (abs($tongSoGioThucTe - $tongSoGioDuKien) > 0.1) {
                return redirect()->back()->withErrors([
                    'tong_so_gio_bien_soan' => 'Tổng số giờ biên soạn (' . $tongSoGioThucTe . ') không khớp với số giờ quy định (' . round($tongSoGioDuKien, 1) . ')'
                ]);
            }
        }

        // Kiểm tra tổng số giờ phản biện nếu đã chọn giờ quy đổi phản biện
        if ($request->id_gio_quy_doi_phan_bien) {
            $gioQuyDoiPhanBien = GioQuyDoi::find($request->id_gio_quy_doi_phan_bien);
            $soLuong = $bienBan->ctDSDangKy->so_luong;
            $gioQuyDoi = $gioQuyDoiPhanBien->gio;
            $soLuongCauQuyDoi = $gioQuyDoiPhanBien->so_luong;
            
            $tongSoGioDuKien = ($soLuong / $soLuongCauQuyDoi) * $gioQuyDoi;
            $tongSoGioThucTe = collect($request->ds_hop)->sum('so_gio');
            
            // Cho phép sai số 0.1
            if (abs($tongSoGioThucTe - $tongSoGioDuKien) > 0.1) {
                return redirect()->back()->withErrors([
                    'tong_so_gio' => 'Tổng số giờ phản biện (' . $tongSoGioThucTe . ') không khớp với số giờ quy định (' . round($tongSoGioDuKien, 1) . ')'
                ]);
            }
        }

        // Cập nhật số giờ cho từng giảng viên biên soạn
        foreach ($request->ds_g_v_bien_soans as $gvData) {
            $gv = $bienBan->ctDSDangKy->dsGVBienSoans->find($gvData['id']);
            if ($gv) {
                $gv->so_gio = $gvData['so_gio'];
                $gv->save();
            }
        }

        // Cập nhật số giờ cho từng người tham gia phản biện
        foreach ($request->ds_hop as $hopData) {
            $hop = DSHop::find($hopData['id']);
            if ($hop && $hop->id_bien_ban_hop === $bienBan->id) {
                $hop->so_gio = $hopData['so_gio'];
                $hop->save();
            }
        }

        return redirect()->route('tbm.dsbienban.index')->with('success', 'Cập nhật số giờ thành công');
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

    /**
     * Gửi email thông báo hoàn thành biên soạn
     */
    public function sendNotification($id)
    {
        Log::info('BIENBAN======================================');
        try {
            // Lấy thông tin biên bản
            $bienBan = BienBanHop::with([
                'ctDSDangKy.hocPhan.boMon',
                'ctDSDangKy.dsGVBienSoans.vienChuc',
                'ctDSDangKy.dsDangKy'
            ])->findOrFail($id);
            Log::info('$bienBan======================================'.$bienBan);

            // Tìm địa chỉ email của nhân viên P.ĐBCL
            $qualityOfficers = User::where('able', 1)->whereHas('roles', function($query) {
                $query->where('name', 'Nhân viên P.ĐBCL');
            })->get();
            if ($qualityOfficers->isEmpty()) {
                return back()->with([
                    'type' => 'error',
                    'message' => 'Không tìm thấy nhân viên Phòng ĐBCL để gửi thông báo'
                ]);
            }

            $nguoiGui = Auth::user();
            $boMon = BoMon::find($nguoiGui->id_bo_mon);
            Log::info($nguoiGui);
            Log::info($boMon);
            Log::info('bắt đầu gửi mail============');
            // Gửi email cho từng nhân viên P.ĐBCL
            foreach ($qualityOfficers as $officer) {
                Mail::to($officer->email)->send(
                    new NotifyCompletedBienSoan($bienBan, $nguoiGui, $boMon)
                );
            }
            // $bienBan->ctDSDangKy->trang_thai = 'SentNT';
            $bienBan->trang_thai = 'Pending';
            $bienBan->save();
            Log::info('gửi mail thành công============');
            return back()->with([
                'type' => 'success',
                'message' => 'Đã gửi thông báo hoàn thành biên soạn đến Phòng ĐBCL'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi gửi thông báo email:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with([
                'type' => 'error',
                'message' => 'Có lỗi xảy ra khi gửi thông báo: ' . $e->getMessage()
            ]);
        }
    }
} 