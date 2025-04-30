<?php

namespace App\Http\Controllers\QualityOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DSDangKy;
use App\Models\CTDSDangKy;
use App\Models\Khoa;
use App\Models\BoMon;
use App\Models\User;
use App\Models\BienBanHop;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ThongKeGiangVienExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class ThongKeGiangVienController extends Controller
{
    public function index(Request $request)
    {
        $role = Auth::user()->getRoleNames();
        Log::info($role);
        if($role->contains('Nhân viên P.ĐBCL')){
            Log::info('Nhân viên P.ĐBCL');
            $role = 'dbcl';
        }
        elseif($role->contains('Admin')){
            Log::info('Admin');
            $role = 'admin';
        }
            $khoa_id = $request->input('khoa_id');
            $bomon_id = $request->input('bomon_id');
            $hoc_ki = $request->input('hoc_ki');
            $nam_hoc = $request->input('nam_hoc');

        // Lấy danh sách khoa và bộ môn
        $khoas = Khoa::where('able', true)
            ->whereNotIn('id', ['admin', 'DBCL'])
            ->get();
            
        $bomons = BoMon::whereHas('khoa', function($query) {
            $query->where('able', true)
                ->whereNotIn('id', ['admin', 'DBCL']);
        })->get();

        // Lấy danh sách năm học và học kỳ
        $ds_nam_hoc = DSDangKy::distinct()->pluck('nam_hoc')->sort()->values();
        $ds_hoc_ki = ['1', '2', 'Hè'];

        // Lấy dữ liệu thống kê
        $thongke_data = $this->layDuLieuThongKe($khoa_id, $bomon_id, $hoc_ki, $nam_hoc);

        return Inertia::render('QualityOffice/ThongKeGiangVien/Index', [
            'role'=>$role,
            'khoas' => $khoas,
            'bomons' => $bomons,
            'ds_nam_hoc' => $ds_nam_hoc,
            'ds_hoc_ki' => $ds_hoc_ki,
            'filters' => [
                'khoa_id' => $khoa_id,
                'bomon_id' => $bomon_id,
                'hoc_ki' => $hoc_ki,
                'nam_hoc' => $nam_hoc
            ],
            'thongke_data' => $thongke_data
        ]);
    }

    private function layDuLieuThongKe($khoa_id, $bomon_id, $hoc_ki, $nam_hoc)
    {
        // Query cơ bản
        $query = DSDangKy::with([
            'ctDSDangKies.hocPhan',
            'ctDSDangKies.dsGVBienSoans.vienChuc',
            'boMon.khoa'
        ])->where('able', true);

        // Áp dụng các bộ lọc
        if ($khoa_id) {
            $query->whereHas('boMon.khoa', function ($q) use ($khoa_id) {
                $q->where('id', $khoa_id);
            });
        }
        if ($bomon_id) {
            $query->where('id_bo_mon', $bomon_id);
        }
        if ($hoc_ki) {
            $query->where('hoc_ki', $hoc_ki);
        }
        if ($nam_hoc) {
            $query->where('nam_hoc', $nam_hoc);
        }

        $dsDangKy = $query->get();

        // Khởi tạo cấu trúc dữ liệu
        $thongke = [
            'tong_hop' => [
                'tong_so_giang_vien' => 0
            ],
            'giang_vien' => []
        ];

        $dsGiangVien = [];
        $dsGiangVienNam = [];
        $dsGiangVienHocKi = [];
        $dsGiangVienKhoa = [];
        $dsGiangVienBoMon = [];

        // Tổ chức dữ liệu theo cấu trúc phân cấp
        foreach ($dsDangKy as $dk) {
            foreach ($dk->ctDSDangKies as $ct) {
                if (!$ct->able) continue;

                $nam_hoc = $dk->nam_hoc;
                $hoc_ki = $dk->hoc_ki;
                $khoa = $dk->boMon->khoa;
                $bo_mon = $dk->boMon;

                // Tính giờ biên soạn
                if ($ct->dsGVBienSoans) {
                    foreach ($ct->dsGVBienSoans as $gvbs) {
                        if (!$gvbs->vienChuc) continue;
                        
                        $giang_vien = $gvbs->vienChuc;
                        $so_gio = $gvbs->so_gio ?? 0;

                        $dsGiangVien[$giang_vien->id] = true;
                        $dsGiangVienNam[$nam_hoc][$giang_vien->id] = true;
                        $dsGiangVienHocKi["{$nam_hoc}_{$hoc_ki}"][$giang_vien->id] = true;
                        $dsGiangVienKhoa["{$nam_hoc}_{$hoc_ki}_{$khoa->id}"][$giang_vien->id] = true;
                        $dsGiangVienBoMon["{$nam_hoc}_{$hoc_ki}_{$khoa->id}_{$bo_mon->id}"][$giang_vien->id] = true;

                        $this->capNhatThongKeGiangVien(
                            $thongke,
                            $nam_hoc,
                            $hoc_ki,
                            $khoa,
                            $bo_mon,
                            $giang_vien,
                            $so_gio,
                            $ct->hocPhan
                        );
                    }
                }
            }
        }

        // Cập nhật tổng số giảng viên
        $thongke['tong_hop']['tong_so_giang_vien'] = count($dsGiangVien);

        // Cập nhật số giảng viên cho từng cấp
        foreach ($thongke['giang_vien'] as $nam_hoc => $nam_data) {
            $thongke['giang_vien'][$nam_hoc]['tong_so_giang_vien'] = count($dsGiangVienNam[$nam_hoc] ?? []);
            
            foreach ($nam_data['hoc_ki'] as $hoc_ki => $hoc_ki_data) {
                $thongke['giang_vien'][$nam_hoc]['hoc_ki'][$hoc_ki]['tong_so_giang_vien'] = count($dsGiangVienHocKi["{$nam_hoc}_{$hoc_ki}"] ?? []);
                
                foreach ($hoc_ki_data['khoa'] as $khoa_id => $khoa_data) {
                    $thongke['giang_vien'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['tong_so_giang_vien'] = count($dsGiangVienKhoa["{$nam_hoc}_{$hoc_ki}_{$khoa_id}"] ?? []);
                    
                    foreach ($khoa_data['bo_mon'] as $bo_mon_id => $bo_mon_data) {
                        $thongke['giang_vien'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['bo_mon'][$bo_mon_id]['tong_so_giang_vien'] = count($dsGiangVienBoMon["{$nam_hoc}_{$hoc_ki}_{$khoa_id}_{$bo_mon_id}"] ?? []);
                    }
                }
            }
        }

        return $thongke;
    }

    private function capNhatThongKeGiangVien(&$thongke, $nam_hoc, $hoc_ki, $khoa, $bo_mon, $giang_vien, $so_gio, $hoc_phan)
    {
        // Cập nhật cấu trúc phân cấp
        if (!isset($thongke['giang_vien'][$nam_hoc])) {
            $thongke['giang_vien'][$nam_hoc] = [
                'hoc_ki' => []
            ];
        }

        if (!isset($thongke['giang_vien'][$nam_hoc]['hoc_ki'][$hoc_ki])) {
            $thongke['giang_vien'][$nam_hoc]['hoc_ki'][$hoc_ki] = [
                'khoa' => []
            ];
        }

        if (!isset($thongke['giang_vien'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id])) {
            $thongke['giang_vien'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id] = [
                'bo_mon' => []
            ];
        }

        if (!isset($thongke['giang_vien'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['bo_mon'][$bo_mon->id])) {
            $thongke['giang_vien'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['bo_mon'][$bo_mon->id] = [
                'giang_vien' => []
            ];
        }

        // Cập nhật thông tin giảng viên
        $gv_index = array_search($giang_vien->id, array_column(
            $thongke['giang_vien'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['bo_mon'][$bo_mon->id]['giang_vien'],
            'id'
        ));

        if ($gv_index === false) {
            $thongke['giang_vien'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['bo_mon'][$bo_mon->id]['giang_vien'][] = [
                'id' => $giang_vien->id,
                'ten' => $giang_vien->name,
                'chi_tiet_mon' => [
                    [
                        'ten_mon' => $hoc_phan->ten,
                        'ma_mon' => $hoc_phan->id,
                        'so_tin_chi' => $hoc_phan->so_tin_chi,
                        'so_gio' => $so_gio
                    ]
                ]
            ];
        } else {
            $gv = &$thongke['giang_vien'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['bo_mon'][$bo_mon->id]['giang_vien'][$gv_index];
            $gv['chi_tiet_mon'][] = [
                'ten_mon' => $hoc_phan->ten,
                'ma_mon' => $hoc_phan->id,
                'so_tin_chi' => $hoc_phan->so_tin_chi,
                'so_gio' => $so_gio
            ];
        }
    }

    public function exportExcel(Request $request)
    {
        $khoa_id = $request->input('khoa_id');
        $bomon_id = $request->input('bomon_id');
        $nam_hoc = $request->input('nam_hoc');
        $hoc_ki = $request->input('hoc_ki');
        
        // Lấy dữ liệu thống kê
        $thongke_data = $this->layDuLieuThongKe($khoa_id, $bomon_id, $hoc_ki, $nam_hoc);
        
        // Tạo tên file Excel
        $filename = 'thong_ke_giang_vien';
        if ($nam_hoc) $filename .= "_nam_{$nam_hoc}";
        if ($hoc_ki) $filename .= "_hk_{$hoc_ki}";
        $filename .= '.xlsx';
        
        // Xuất Excel
        return Excel::download(new ThongKeGiangVienExport($thongke_data), $filename);
    }
} 