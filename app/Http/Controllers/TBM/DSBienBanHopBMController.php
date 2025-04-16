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

class DSBienBanHopBMController extends Controller
{
    /**
     * Hiển thị danh sách biên bản họp bộ môn
     */
    public function index(Request $request)
    {
        $query = BienBanHop::with([
            'ctDSDangKy.dsDangKy.boMon',
            'ctDSDangKy.hocPhan',
            'ctDSDangKy.vienChuc'
        ])
        ->whereHas('ctDSDangKy.hocPhan', function($query) {
            $query->where('id_bo_mon', Auth::user()->id_bo_mon);
        })
        ->join('c_t_d_s_dang_kies', 'bien_ban_hops.id_ct_ds_dang_ky', '=', 'c_t_d_s_dang_kies.id')
        ->join('d_s_dang_kies', 'c_t_d_s_dang_kies.id_ds_dang_ky', '=', 'd_s_dang_kies.id')
        ->orderBy('d_s_dang_kies.created_at', 'desc')
        ->orderBy('d_s_dang_kies.id', 'desc')
        ->select('bien_ban_hops.*');

        // Lọc theo học kỳ nếu có
        if ($request->has('hoc_ki') && $request->hoc_ki != '') {
            $query->whereHas('ctDSDangKy.dsDangKy', function($q) use ($request) {
                $q->where('hoc_ki', $request->hoc_ki);
            });
        }

        // Lọc theo năm học nếu có
        if ($request->has('nam_hoc') && $request->nam_hoc != '') {
            $query->whereHas('ctDSDangKy.dsDangKy', function($q) use ($request) {
                $q->where('nam_hoc', $request->nam_hoc);
            });
        }

        $dsBienBan = $query->orderBy('created_at', 'desc')->get();

        // Lấy danh sách học kỳ và năm học để hiển thị trong select
        $dsHocKi = DSDangKy::select('hoc_ki')
            ->where('id_bo_mon', Auth::user()->id_bo_mon)
            ->distinct()
            ->orderBy('hoc_ki')
            ->pluck('hoc_ki');

        $dsNamHoc = DSDangKy::select('nam_hoc', 'hoc_ki')
            ->where('id_bo_mon', Auth::user()->id_bo_mon)
            ->distinct()
            ->orderBy('nam_hoc', 'desc')
            ->get()
            ->map(function($item) {
                return $item->nam_hoc;
            })
            ->unique()
            ->values();

        return Inertia::render('TBM/DSBienBanHopBM/Index', [
            'ds_bien_ban' => $dsBienBan,
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
        $ctDSDangKies = CTDSDangKy::with(['hocPhan', 'vienChuc', 'dsDangKy'])
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

        // Lấy các nhiệm vụ cụ thể từ bảng NhiemVu
        $nhiemVus = NhiemVu::whereIn('ten', ['Chủ tịch', 'Thư ký', 'Cán bộ phản biện'])
            ->where('able', 1)
            ->get();

        return Inertia::render('TBM/DSBienBanHopBM/Create', [
            'ct_ds_dang_kies' => $ctDSDangKies,
            'vien_chucs' => $vienChucs,
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
                'able' => true
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
        $bienBan = BienBanHop::with(['ctDSDangKy.hocPhan', 'ctDSDangKy.vienChuc', 'ctDSDangKy.dsDangKy', 'dsHop'])
            ->findOrFail($id);

        // Kiểm tra quyền truy cập
        if ($bienBan->ctDSDangKy->hocPhan->id_bo_mon !== Auth::user()->id_bo_mon) {
            return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa biên bản này');
        }

        // Lấy danh sách viên chức của bộ môn
        $vienChucs = User::where('id_bo_mon', Auth::user()->id_bo_mon)
            ->where('able', 1)
            ->get();

        // Lấy các nhiệm vụ cụ thể từ bảng NhiemVu
        $nhiemVus = NhiemVu::whereIn('ten', ['Chủ tịch', 'Thư ký', 'Cán bộ phản biện'])
            ->where('able', 1)
            ->get();

        return Inertia::render('TBM/DSBienBanHopBM/Edit', [
            'bien_ban' => $bienBan,
            'vien_chucs' => $vienChucs,
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

            $bienBan = BienBanHop::with(['ctDSDangKy.hocPhan', 'ctDSDangKy.vienChuc', 'ctDSDangKy.dsDangKy'])
                ->findOrFail($id);

            // Tạo tên file theo format: hoc_phan_giang_vien_hoc_ki_nam.pdf
            $fileName = Str::slug(
                $bienBan->ctDSDangKy->hocPhan->ten . '_' . 
                $bienBan->ctDSDangKy->vienChuc->name . '_' .
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

            return back()->with([
                'type' => 'success',
                'message' => 'Upload nội dung thành công!'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi upload file:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->with([
                'type' => 'error',
                'message' => 'Có lỗi xảy ra khi upload file: ' . $e->getMessage()
            ]);
        }
    }

    public function editSoGio($id)
    {
        $bienBan = BienBanHop::with([
            'ctDSDangKy.hocPhan',
            'ctDSDangKy.vienChuc',
            'dsHop' => function($query) {
                $query->with(['vienChuc', 'nhiemVu'])->where('able', true);
            }
        ])->findOrFail($id);

        return Inertia::render('TBM/DSBienBanHopBM/EditSoGio', [
            'bien_ban' => $bienBan
        ]);
    }

    public function updateSoGio(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'so_gio_bien_soan' => 'required|numeric|min:0',
                'ds_hop.*.so_gio' => 'required|numeric|min:0'
            ]);
            Log::info('Request data:', $validated);

            DB::beginTransaction();

            // Cập nhật số giờ cho người biên soạn
            $bienBan = BienBanHop::with('ctDSDangKy')->findOrFail($id);
            $bienBan->ctDSDangKy->update([
                'so_gio' => $request->so_gio_bien_soan
            ]);
            Log::info('Đã cập nhật số giờ cho người biên soạn:', $bienBan->toArray());

            // Cập nhật số giờ cho thành viên tham gia họp
            foreach ($request->ds_hop as $thanhVien) {
                DSHop::where('id', $thanhVien['id'])->update([
                    'so_gio' => $thanhVien['so_gio']
                ]);
                Log::info('Đã cập nhật số giờ cho thành viên:', $thanhVien);
            }

            DB::commit();

            return redirect()->route('tbm.dsbienban.index')->with([
                'type' => 'success',
                'message' => 'Cập nhật số giờ thành công!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with([
                'type' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
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
            return back()->with([
                'type' => 'error',
                'message' => 'Có lỗi xảy ra khi tải xuống file: ' . $e->getMessage()
            ]);
        }
    }
} 