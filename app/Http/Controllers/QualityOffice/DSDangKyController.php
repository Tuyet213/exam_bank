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
        $query = DSDangKy::with(['boMon.khoa', 'ctDSDangKies']);
        
        // Tìm kiếm theo từ khóa
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('boMon', function($q) use ($search) {
                $q->where('ten', 'like', "%{$search}%");
            })
            ->orWhere('nam_hoc', 'like', "%{$search}%")
            ->orWhere('hoc_ki', 'like', "%{$search}%");
        }
        
        // Lọc theo khoa
        if ($request->has('khoa_id') && !empty($request->khoa_id)) {
            $query->whereHas('boMon', function($q) use ($request) {
                $q->where('id_khoa', $request->khoa_id);
            });
        }
        
        // Lọc theo bộ môn
        if ($request->has('bomon_id') && !empty($request->bomon_id)) {
            $query->where('id_bo_mon', $request->bomon_id);
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
                $ds->status = 'Pending';
            } else if ($ds->ctDSDangKies->contains('trang_thai', 'Rejected')) {
                $ds->status = 'Rejected';
            } else if ($ds->ctDSDangKies->every(function ($ct) {
                return $ct->trang_thai === 'Approved';
            })) {
                $ds->status = 'Approved';
            } else {
                $ds->status = 'Pending';
            }
        });

        // Tổ chức dữ liệu theo cấu trúc phân cấp: năm học -> học kỳ -> danh sách đăng ký
        $danhSachHierarchy = [];
        
        foreach ($danhSachDangKy as $dsDangKy) {
            $namHoc = $dsDangKy->nam_hoc;
            $hocKi = $dsDangKy->hoc_ki;
            
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
                    'danh_sach' => []
                ];
            }
            
            // Thêm danh sách đăng ký vào học kỳ tương ứng
            $danhSachHierarchy[$namHoc]['hoc_ki'][$hocKi]['danh_sach'][] = $dsDangKy;
        }
        
        // Sắp xếp theo năm học mới nhất trước
        krsort($danhSachHierarchy);
        
        // Lấy danh sách khoa (loại trừ admin và DBCL)
        $khoas = Khoa::whereNotIn('id', ['admin', 'DBCL'])
            ->orderBy('ten')
            ->get();
        
        // Lấy danh sách bộ môn (loại trừ admin và dbcl)
        // Đảm bảo chỉ lấy bộ môn thuộc các khoa hợp lệ
        $boMons = BoMon::with('khoa')
            ->whereNotIn('id', ['admin', 'dbcl'])
            ->whereHas('khoa', function($query) {
                $query->whereNotIn('id', ['admin', 'DBCL']);
            })
            ->orderBy('ten')
            ->get();
        
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
            'dsdangky' => $danhSachDangKy,
            'dsdangky_hierarchy' => $danhSachHierarchy,
            'khoas' => $khoas,
            'bomons' => $boMons,
            'ds_hoc_ki' => $dsHocKi,
            'ds_nam_hoc' => $dsNamHoc,
            'filters' => $request->only(['search', 'khoa_id', 'bomon_id', 'hoc_ki', 'nam_hoc']),
        ]);
    }
} 