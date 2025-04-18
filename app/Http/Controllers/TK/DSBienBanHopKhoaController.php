<?php

namespace App\Http\Controllers\TK;

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
use App\Models\BoMon;
use App\Models\Khoa;

class DSBienBanHopKhoaController extends Controller
{
    /**
     * Hiển thị danh sách biên bản họp khoa
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

        $query = BienBanHop::with([
            'dsDangKy.boMon',
            'dsDangKy.boMon.khoa'
        ])
        ->join('d_s_dang_kies', 'bien_ban_hops.id_ds_dang_ky', '=', 'd_s_dang_kies.id')
        ->join('bo_mons', 'd_s_dang_kies.id_bo_mon', '=', 'bo_mons.id')
        ->where('bo_mons.id_khoa', $idKhoa)
        ->orderBy('bien_ban_hops.thoi_gian', 'desc')
        ->select('bien_ban_hops.*');

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

        $dsBienBan = $query->get();

        // Lấy danh sách bộ môn của khoa
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

        return Inertia::render('TK/DSBienBanHopKhoa/Index', [
            'ds_bien_ban' => $dsBienBan,
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

    /**
     * Hiển thị chi tiết biên bản
     */
    public function show($id)
    {
        $bienBan = BienBanHop::with([
            'ctDSDangKy.dsDangKy.boMon.khoa',
            'ctDSDangKy.hocPhan',
            'ctDSDangKy.vienChuc',
            'dsHop.vienChuc',
            'dsHop.nhiemVu'
        ])->findOrFail($id);

        // Kiểm tra quyền truy cập
        if ($bienBan->ctDSDangKy->dsDangKy->boMon->khoa->id !== Auth::user()->id_khoa) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('TK/DSBienBanHopKhoa/Show', [
            'bien_ban' => $bienBan
        ]);
    }

    /**
     * Tải xuống nội dung biên bản
     */
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

    /**
     * Hiển thị form tạo biên bản họp
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $boMon = BoMon::find($user->id_bo_mon);
        $idKhoa = $boMon ? $boMon->id_khoa : null;
        $idDbcl = 'DBCL';
        
        // Lấy danh sách đăng ký được chọn
        $dsDangKyIds = $request->input('ds_dang_ky_ids', []);
        
        // Lấy thông tin chi tiết
        $dsDangKies = DSDangKy::with([
            'boMon', 
            'boMon.khoa',
            'ctDSDangKies.hocPhan',
            'ctDSDangKies.vienChuc'
        ])
            ->whereIn('id', $dsDangKyIds)
            ->get();
 
        // Kiểm tra quyền truy cập
        foreach ($dsDangKies as $dk) {
            if ($dk->boMon->id_khoa !== $idKhoa) {
                return redirect()->back()->with('error', 'Bạn không có quyền tạo biên bản cho đăng ký này');
            }
        }

        $user = Auth::user();
        $boMon = BoMon::find($user->id_bo_mon);
        $idKhoa = $boMon ? $boMon->id_khoa : null;
        $boMons = BoMon::where('id_khoa', $idKhoa)->get();
        $boMonsDBCL = BoMon::where('id_khoa', $idDbcl)->get();
        // Lấy danh sách ID của các bộ môn
        $boMonIds = $boMons->pluck('id')->toArray();

        // Lấy danh sách viên chức của khoa
        $vienChucs = User::with('boMon')
            ->whereIn('id_bo_mon', $boMonIds)
            ->where('able', 1)
            ->get();

        // Lấy danh sách viên chức DBCL
        $uyViens = User::with('boMon')->whereIn('id_bo_mon', $boMonsDBCL->pluck('id')->toArray())->get();

        // Lấy các nhiệm vụ cụ thể từ bảng NhiemVu
        $nhiemVus = NhiemVu::whereIn('ten', ['Chủ tịch', 'Thư ký', 'Ủy viên'])
            ->where('able', 1)
            ->get();
 
        return Inertia::render('TK/DSBienBanHopKhoa/Create', [
                'ds_dang_kies' => $dsDangKies,
            'vien_chucs' => $vienChucs,
            'vien_chucs_dbcl' => $uyViens,
            'nhiem_vus' => $nhiemVus
        ]);
    }

    /**
     * Lưu biên bản họp mới
     */
    public function store(Request $request)
    {
        // try {
            // Log request data để debug
            //Log::info('Request data:', $request->all());
            // Validate dữ liệu cơ bản
            $validated = $request->validate([
                'id_ds_dang_ky' => 'required|exists:d_s_dang_kies,id',
                'thoi_gian' => 'required|date_format:Y-m-d\TH:i',
                'dia_diem' => 'required|string',
                'ds_hop' => 'required|array|min:5',
                 'ds_hop.*.id_vien_chuc' => 'required|exists:users,id',
                'ds_hop.*.id_nhiem_vu' => 'required|exists:nhiem_vus,id',
            ]);
            Log::info(' VALIDATED..................', $validated);
            DB::beginTransaction();

            // Tạo biên bản họp
            $bienBan = BienBanHop::create([
                'id_ds_dang_ky' => $request->id_ds_dang_ky,
                'noi_dung' => null,
                'thoi_gian' => $request->thoi_gian,
                'dia_diem' => $request->dia_diem,
                'cap' => 'Khoa',
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

        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::error('Lỗi tạo biên bản họp:', [
        //         'message' => $e->getMessage(),
        //         'file' => $e->getFile(),
        //         'line' => $e->getLine(),
        //         'trace' => $e->getTraceAsString()
        //     ]);
        //     return back()->with([
        //         'type' => 'error',
        //         'message' => 'Có lỗi xảy ra khi tạo biên bản họp: ' . $e->getMessage()
        //     ]);
        // }
    }

    /**
     * Lấy ID của nhiệm vụ theo tên
     */

    /**
     * Hiển thị form chỉnh sửa biên bản
     */
    public function edit($id)
    {
        $user = Auth::user();
        $boMon = BoMon::find($user->id_bo_mon);
        $idKhoa = $boMon ? $boMon->id_khoa : null;

        $bienBan = BienBanHop::with([
            'dsDangKy.boMon', 
            'dsDangKy.ctDSDangKies.hocPhan',
            'dsDangKy.ctDSDangKies.vienChuc',
            'dsHop.vienChuc', 
            'dsHop.nhiemVu'
        ])->findOrFail($id);
       
        $user = Auth::user();
        $boMon = BoMon::find($user->id_bo_mon);
        $idKhoa = $boMon ? $boMon->id_khoa : null;
        $idDbcl = 'DBCL';
        $boMons = BoMon::where('id_khoa', $idKhoa)->get();
        $boMonsDBCL = BoMon::where('id_khoa', $idDbcl)->get();
        // Lấy danh sách ID của các bộ môn
        $boMonIds = $boMons->pluck('id')->toArray();
        
        // Lấy danh sách viên chức của khoa
        $vienChucs = User::with('boMon')
            ->whereIn('id_bo_mon', $boMonIds)
            ->where('able', 1)
            ->get();

        // Lấy danh sách viên chức DBCL
        $uyViens = User::with('boMon')->whereIn('id_bo_mon', $boMonsDBCL->pluck('id')->toArray())->get();

        // Lấy các nhiệm vụ cụ thể từ bảng NhiemVu
        $nhiemVus = NhiemVu::whereIn('ten', ['Chủ tịch', 'Thư ký', 'Ủy viên'])
            ->where('able', 1)
            ->get();

        return Inertia::render('TK/DSBienBanHopKhoa/Edit', [
            'bien_ban' => $bienBan,
            'vien_chucs' => $vienChucs,
            'vien_chucs_dbcl' => $uyViens,
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
                'ds_hop' => 'required|array|min:5',
                'ds_hop.*.id_vien_chuc' => 'required|exists:users,id',
                'ds_hop.*.id_nhiem_vu' => 'required|exists:nhiem_vus,id'
            ]);
            Log::info('Validated data:', $validated);

            DB::beginTransaction();

            // Cập nhật thông tin biên bản
            $bienBan = BienBanHop::findOrFail($id);
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
                    // Thêm thành viên mới
                    $dsThanhVien = $bienBan->dsHop()->create([
                        'id_bien_ban_hop' => $bienBan->id,
                        'id_vien_chuc' => $thanhVien['id_vien_chuc'],
                        'id_nhiem_vu' => $thanhVien['id_nhiem_vu'],
                        'able' => true
                    ]);
                    Log::info('Đã thêm thành viên mới:', $dsThanhVien->toArray());
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
     * Upload nội dung biên bản họp
     */
    public function uploadNoiDung(Request $request, $id)
    {
        try {
            $request->validate([
                'noi_dung' => 'required|mimes:pdf|max:10240', // max 10MB
            ]);

            $bienBan = BienBanHop::with(['dsDangKy.boMon'])
                ->findOrFail($id);

            // Tạo tên file theo format: bo_mon_hoc_ki_nam.pdf
            $fileName = Str::slug(
                $bienBan->dsDangKy->boMon->ten . '_' . 
                $bienBan->dsDangKy->hoc_ki . '_' .
                $bienBan->dsDangKy->nam_hoc . '_' .
                date('YmdHis')
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

    /**
     * Hiển thị form chỉnh sửa số giờ
     */
    public function editSoGio($id)
    {
        $user = Auth::user();
        $boMon = BoMon::find($user->id_bo_mon);
        $idKhoa = $boMon ? $boMon->id_khoa : null;

        $bienBan = BienBanHop::with([
            'dsDangKy.boMon',
            'dsHop' => function($query) {
                $query->with(['vienChuc', 'nhiemVu'])->where('able', true);
            }
        ])->findOrFail($id);

        // Kiểm tra quyền truy cập
        if ($bienBan->dsDangKy->boMon->id_khoa !== $idKhoa) {
            return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa số giờ cho biên bản này');
        }

        return Inertia::render('TK/DSBienBanHopKhoa/EditSoGio', [
            'bien_ban' => $bienBan
        ]);
    }

    /**
     * Cập nhật số giờ cho biên bản
     */
    public function updateSoGio(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'ds_hop.*.id' => 'required|exists:d_s_hops,id',
                'ds_hop.*.so_gio' => 'required|numeric|min:0'
            ]);
            Log::info('Request data:', $validated);

            DB::beginTransaction();

            $bienBan = BienBanHop::findOrFail($id);

            // Cập nhật số giờ cho thành viên tham gia họp
            foreach ($request->ds_hop as $thanhVien) {
                $dsHop = DSHop::where('id', $thanhVien['id'])
                    ->where('id_bien_ban_hop', $bienBan->id)
                    ->first();
                
                if ($dsHop) {
                    $dsHop->update([
                        'so_gio' => $thanhVien['so_gio']
                    ]);
                    Log::info('Đã cập nhật số giờ cho thành viên:', [
                        'id' => $dsHop->id,
                        'id_vien_chuc' => $dsHop->id_vien_chuc,
                        'so_gio' => $dsHop->so_gio
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('tk.dsbienban.index')->with([
                'type' => 'success',
                'message' => 'Cập nhật số giờ thành công!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật số giờ:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->with([
                'type' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }
} 