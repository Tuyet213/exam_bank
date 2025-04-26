<?php

namespace App\Http\Controllers\TBM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DSDangKy;
use App\Models\HocPhan;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\DSDangKyMail;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CTDSDangKy;
use App\Models\DSGVBienSoan;

class DSDangKyController extends Controller
{
    public function index(Request $request)
    {
        // Lấy bộ môn của trưởng bộ môn đang đăng nhập
        $user = Auth::user();
        $boMon = $user->boMon;

        if (!$boMon) {
            return redirect()->back()->with('error', 'Bạn chưa được phân công quản lý bộ môn nào!');
        }

        // Lấy tất cả danh sách đăng ký của bộ môn
        $query = DSDangKy::with(['boMon', 'ctDSDangKies'])
            ->where('id_bo_mon', $boMon->id);

        // Lọc theo học kỳ nếu có
        if ($request->has('hoc_ki') && !empty($request->hoc_ki)) {
            $query->where('hoc_ki', $request->hoc_ki);
        }

        // Lọc theo năm học nếu có
        if ($request->has('nam_hoc') && !empty($request->nam_hoc)) {
            $query->where('nam_hoc', $request->nam_hoc);
        }

        // Tìm kiếm theo từ khóa nếu có
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ten', 'like', "%{$search}%")
                  ->orWhere('nam_hoc', 'like', "%{$search}%")
                  ->orWhere('hoc_ki', 'like', "%{$search}%");
            });
        }

        $danhSachDangKy = $query->orderBy('nam_hoc', 'desc')
            ->orderBy('hoc_ki', 'asc')
            ->get();

        // Xử lý trạng thái và quyền chỉnh sửa
        $danhSachDangKy->transform(function ($item) {
            // Thêm trạng thái
            if ($item->da_gui) {
                $item->trang_thai = 'Sent';
            } else if ($item->ctDSDangKies->isEmpty()) {
                $item->trang_thai = 'Draft';
            } else if ($item->ctDSDangKies->contains('trang_thai', 'Rejected')) {
                $item->trang_thai = 'Rejected';
            } else if ($item->ctDSDangKies->every(function ($ct) {
                return $ct->trang_thai === 'Approved';
            })) {
                $item->trang_thai = 'Approved';
            } else {
                $item->trang_thai = 'Draft';
            }

            // Có thể gửi nếu chưa gửi và có ít nhất 1 chi tiết
            $item->can_send = !$item->da_gui && $item->ctDSDangKies->count() > 0;
            
            return $item;
        });

        // Tổ chức dữ liệu theo cấu trúc phân cấp: năm học -> học kỳ -> danh sách đăng ký
        $danhSachPhierarchy = [];
        
        foreach ($danhSachDangKy as $dsDangKy) {
            $namHoc = $dsDangKy->nam_hoc;
            $hocKi = $dsDangKy->hoc_ki;
            
            // Tạo năm học nếu chưa tồn tại
            if (!isset($danhSachPhierarchy[$namHoc])) {
                $danhSachPhierarchy[$namHoc] = [
                    'ten' => $namHoc,
                    'hoc_ki' => []
                ];
            }
            
            // Tạo học kỳ nếu chưa tồn tại
            if (!isset($danhSachPhierarchy[$namHoc]['hoc_ki'][$hocKi])) {
                $danhSachPhierarchy[$namHoc]['hoc_ki'][$hocKi] = [
                    'ten' => 'Học kỳ ' . $hocKi,
                    'danh_sach' => []
                ];
            }
            
            // Thêm danh sách đăng ký vào học kỳ tương ứng
            $danhSachPhierarchy[$namHoc]['hoc_ki'][$hocKi]['danh_sach'][] = $dsDangKy;
        }
        
        // Sắp xếp theo năm học mới nhất trước
        krsort($danhSachPhierarchy);

        // Lọc danh sách học kỳ và năm học
        $dsHocKi = ['1', '2', 'Hè'];
        
        $currentYear = date('Y');
        $dsNamHoc = [];
        for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++) {
            $dsNamHoc[] = $i . '-' . ($i + 1);
        }

        return Inertia::render('TBM/DSDangKy/Index', [
            'danhsachs_hierarchy' => $danhSachPhierarchy,
            'bo_mon' => $boMon->ten,
            'ds_hoc_ki' => $dsHocKi,
            'ds_nam_hoc' => $dsNamHoc,
            'filters' => $request->only(['search', 'hoc_ki', 'nam_hoc']),
        ]);
    }

    public function send($id)
    {
        $dsdangky = DSDangKy::with(['boMon', 'ctDSDangKies.vienChuc', 'ctDSDangKies.hocPhan'])
            ->findOrFail($id);

        // Cập nhật trạng thái
        foreach($dsdangky->ctDSDangKies as $ct) {
            $ct->trang_thai = 'Pending';
            $ct->save();
        }

        // Gửi mail
        $receivers = User::where('able', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Nhân viên P.ĐBCL');
            })
            ->get();

        // Tìm trưởng bộ môn
        $truongBoMon = User::where('id_bo_mon', $dsdangky->id_bo_mon)
            ->whereHas('roles', function($query) {
                $query->where('name', 'Trưởng bộ môn');
            })
            ->first();

        $tenNguoiGui = $truongBoMon ? $truongBoMon->name : $dsdangky->boMon->ten;

        foreach ($receivers as $receiver) {
            Mail::to($receiver->email)->send(new DSDangKyMail(
                "Đăng ký xây dựng ngân hàng câu hỏi/đề thi " . $dsdangky->boMon->ten,
                $dsdangky->boMon->ten,
                $tenNguoiGui,
                $dsdangky->ctDSDangKies,
                $dsdangky
            ));
        }

        return redirect()->back()
            ->with('message', 'Gửi danh sách đăng ký thành công!')
            ->with('success', true);
    }

    public function create()
    {
        $hocPhans = HocPhan::where('id_bo_mon', Auth::user()->id_bo_mon)
            ->orderBy('id')
            ->get();
            
        $vienChucs = User::where('id_bo_mon', Auth::user()->id_bo_mon)->orderBy('name')->get();
        

        return Inertia::render('TBM/DSDangKy/Create', [
            'hoc_phans' => $hocPhans,
            'vien_chucs' => $vienChucs,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hoc_ki' => 'required|string|in:1,2,Hè',
            'chi_tiet' => 'required|array|min:1',
            'chi_tiet.*.id_hoc_phan' => 'required|exists:hoc_phans,id',
            'chi_tiet.*.id_vien_chuc' => 'required|array|min:1',
            'chi_tiet.*.id_vien_chuc.*' => 'exists:users,id',
            'chi_tiet.*.loai_ngan_hang' => 'required|in:1, 0',
            'chi_tiet.*.so_luong' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Tạo danh sách đăng ký
            $nam = now()->year;
            $dsDangKy = DSDangKy::create([
                'hoc_ki' => $request->hoc_ki,
                'nam_hoc' => $request->hoc_ki == '1' ? $nam . '-' . ($nam + 1) :  ($nam - 1) . '-' . $nam,
                'id_bo_mon' => Auth::user()->id_bo_mon,
                'trang_thai' => 'Draft'
            ]);

            // Tạo chi tiết danh sách
            foreach ($request->chi_tiet as $ct) {
                // Tạo bản ghi CTDSDangKy
                $ctDSDangKy = CTDSDangKy::create([
                    'id_ds_dang_ky' => $dsDangKy->id,
                    'id_hoc_phan' => $ct['id_hoc_phan'],
                    'loai_ngan_hang' => $ct['loai_ngan_hang'],
                    'so_luong' => $ct['so_luong'],
                    'trang_thai' => 'Draft',
                    'so_gio' => 0
                ]);
                
                // Tạo bản ghi DSGVBienSoan cho từng viên chức
                foreach ($ct['id_vien_chuc'] as $idVienChuc) {
                    DSGVBienSoan::create([
                        'id_ct_ds_dang_ky' => $ctDSDangKy->id,
                        'id_vien_chuc' => $idVienChuc
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('tbm.dsdangky.index')
                ->with('success', true)
                ->with('message', 'Tạo danh sách đăng ký thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('success', false)
                ->with('message', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $dsdangky = DSDangKy::with(['boMon', 'ctDSDangKies.vienChuc', 'ctDSDangKies.hocPhan'])
            ->findOrFail($id);
        return Inertia::render('TBM/DSDangKy/Edit', compact('dsdangky'));
    }

    public function update(Request $request, $id)
    {
        $dsdangky = DSDangKy::findOrFail($id);
        $dsdangky->update($request->all());
        return redirect()->route('tbm.dsdangky.index')->with('message', 'Cập nhật danh sách đăng ký thành công!');
    }
   
    
}
