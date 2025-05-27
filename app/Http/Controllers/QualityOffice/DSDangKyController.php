<?php

namespace App\Http\Controllers\QualityOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\DSDangKy;
use App\Models\CTDSDangKy;
use App\Models\Khoa;
use App\Models\BoMon;

class DSDangKyController extends Controller
{
    public function index(Request $request)
    {
        // Lọc dữ liệu theo request
        $query = DSDangKy::with(['boMon.khoa', 'ctDSDangKies'])->where('d_s_dang_kies.able', true);
        
        // Tìm kiếm theo từ khóa
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('boMon', function($q) use ($search) {
                $q->where('ten', 'like', "%{$search}%")
                  ->where('able', true);
            })
            ->orWhere('nam_hoc', 'like', "%{$search}%")
            ->orWhere('hoc_ki', 'like', "%{$search}%");
        }
        
        // Lọc theo khoa
        if ($request->has('khoa') && !empty($request->khoa)) {
            $query->whereHas('boMon.khoa', function($q) use ($request) {
                $q->where('ten', $request->khoa)
                  ->where('able', true);
            });
        }
        
        // Lọc theo bộ môn
        if ($request->has('bo_mon') && !empty($request->bo_mon)) {
            $query->whereHas('boMon', function($q) use ($request) {
                $q->where('ten', $request->bo_mon)
                  ->where('able', true);
            });
        }
        
        // Lọc theo học kỳ
        if ($request->has('hoc_ki') && !empty($request->hoc_ki)) {
            $query->where('hoc_ki', $request->hoc_ki);
        }
        
        // Lọc theo năm học
        if ($request->has('nam_hoc') && !empty($request->nam_hoc)) {
            $query->where('nam_hoc', $request->nam_hoc);
        }

        $danhSachDangKy = $query->orderBy('nam_hoc', 'desc')
            ->orderBy('hoc_ki', 'asc')
            ->get();
        
        // Xử lý trạng thái cho từng DSDangKy
        $danhSachDangKy->each(function ($ds) {
            if ($ds->ctDSDangKies->isEmpty()) {
                $ds->trang_thai = 'Pending';
            } else if ($ds->ctDSDangKies->contains('trang_thai', 'Rejected')) {
                $ds->trang_thai = 'Rejected';
            } else if ($ds->ctDSDangKies->every(function ($ct) {
                return $ct->trang_thai === 'Approved';
            })) {
                $ds->trang_thai = 'Approved';
            } else {
                $ds->trang_thai = 'Pending';
            }
        });

        // Tổ chức dữ liệu theo cấu trúc phân cấp: năm học -> học kỳ -> khoa -> bộ môn -> danh sách đăng ký
        $danhSachHierarchy = [];
        
        foreach ($danhSachDangKy as $dsDangKy) {
            $namHoc = $dsDangKy->nam_hoc;
            $hocKi = $dsDangKy->hoc_ki;
            $khoaTen = $dsDangKy->boMon->khoa->ten;
            $khoaId = $dsDangKy->boMon->khoa->id;
            $boMonTen = $dsDangKy->boMon->ten;
            $boMonId = $dsDangKy->boMon->id;
            
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
            
            // Thêm danh sách đăng ký vào bộ môn tương ứng
            $danhSachHierarchy[$namHoc]['hoc_ki'][$hocKi]['khoa'][$khoaId]['bo_mon'][$boMonId]['danh_sach'][] = $dsDangKy;
        }
        
        // Sắp xếp theo năm học mới nhất trước
        krsort($danhSachHierarchy);
        
        // Lấy danh sách khoa (loại trừ admin và DBCL)
        $khoas = Khoa::whereNotIn('id', ['admin', 'DBCL'])
            ->where('able', true)
            ->orderBy('ten')
            ->get();
        
        // Danh sách tên khoa
        $dsKhoa = $khoas->pluck('ten')->toArray();
        
        // Lấy danh sách bộ môn (loại trừ admin và dbcl)
        // Đảm bảo chỉ lấy bộ môn thuộc các khoa hợp lệ
        $boMons = BoMon::with('khoa')
            ->whereNotIn('id', ['admin', 'dbcl'])
            ->where('able', true)
            ->whereHas('khoa', function($query) {
                $query->whereNotIn('id', ['admin', 'DBCL'])
                      ->where('able', true);
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

        return Inertia::render('QualityOffice/DSDangKy/Index', [
            'danhsachs_hierarchy' => $danhSachHierarchy,
            'ds_khoa' => $dsKhoa,
            'ds_bo_mon' => $dsBoMon,
            'bo_mon_theo_khoa' => $boMonTheoKhoa,
            'ds_hoc_ki' => $dsHocKi,
            'ds_nam_hoc' => $dsNamHoc,
            'filters' => $request->only(['search', 'khoa', 'bo_mon', 'hoc_ki', 'nam_hoc']),
        ]);
    }
} 