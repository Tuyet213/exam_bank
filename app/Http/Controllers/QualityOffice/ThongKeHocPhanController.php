<?php

namespace App\Http\Controllers\QualityOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DSDangKy;
use App\Models\CTDSDangKy;
use App\Models\Khoa;
use App\Models\BoMon;
use App\Models\User;
use App\Models\HocPhan;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ThongKeHocPhanExport;
use Illuminate\Support\Facades\Auth;

class ThongKeHocPhanController extends Controller
{
    public function index(Request $request)
    {
        $role = Auth::user()->getRoleNames();
        
        if($role->contains('Nhân viên P.ĐBCL')){
           
            $role = 'dbcl';
        }
        elseif($role->contains('Admin')){
           
            $role = 'admin';
        }
        $khoa_id = $request->input('khoa_id');
        $bomon_id = $request->input('bomon_id');
        $hoc_ki = $request->input('hoc_ki');
        $nam_hoc = $request->input('nam_hoc');
        $bac_dao_tao = $request->input('bac_dao_tao');

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
        $thongke_data = $this->layDuLieuThongKe($khoa_id, $bomon_id, $hoc_ki, $nam_hoc, $bac_dao_tao);

        return Inertia::render('QualityOffice/ThongKeHocPhan/Index', [
            'khoas' => $khoas,
            'bomons' => $bomons,
            'ds_nam_hoc' => $ds_nam_hoc,
            'ds_hoc_ki' => $ds_hoc_ki,
            'filters' => [
                'khoa_id' => $khoa_id,
                'bomon_id' => $bomon_id,
                'hoc_ki' => $hoc_ki,
                'nam_hoc' => $nam_hoc,
                'bac_dao_tao' => $bac_dao_tao
            ],
            'thongke_data' => $thongke_data
        ]);
    }

    private function layDuLieuThongKe($khoa_id, $bomon_id, $hoc_ki, $nam_hoc, $bac_dao_tao)
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
                'tong_so_hoc_phan' => 0,
                'tong_so_giang_vien' => 0,
                'tong_so_gio' => 0
            ],
            'nam_hoc' => []
        ];

        $dsHocPhan = [];
        $dsGiangVien = [];

        // Tổ chức dữ liệu theo cấu trúc phân cấp
        foreach ($dsDangKy as $dk) {
            foreach ($dk->ctDSDangKies as $ct) {
                if (!$ct->able || !$ct->hocPhan) continue;

                $nam_hoc = $dk->nam_hoc;
                $hoc_ki = $dk->hoc_ki;
                $khoa = $dk->boMon->khoa;
                $bo_mon = $dk->boMon;
                $hoc_phan = $ct->hocPhan;

                // Cập nhật danh sách học phần và giảng viên
                $dsHocPhan[$hoc_phan->id] = true;

                if ($ct->dsGVBienSoans) {
                    foreach ($ct->dsGVBienSoans as $gvbs) {
                        if (!$gvbs->vienChuc) continue;
                        $dsGiangVien[$gvbs->vienChuc->id] = true;
                    }
                }

                $this->capNhatThongKeHocPhan(
                    $thongke,
                    $nam_hoc,
                    $hoc_ki,
                    $khoa,
                    $bo_mon,
                    $hoc_phan,
                    $ct
                );
            }
        }

        // Cập nhật tổng hợp
        $thongke['tong_hop']['tong_so_hoc_phan'] = count($dsHocPhan);
        $thongke['tong_hop']['tong_so_giang_vien'] = count($dsGiangVien);

        return $thongke;
    }

    private function capNhatThongKeHocPhan(&$thongke, $nam_hoc, $hoc_ki, $khoa, $bo_mon, $hoc_phan, $ct)
    {
        // Cập nhật cấu trúc phân cấp
        if (!isset($thongke['nam_hoc'][$nam_hoc])) {
            $thongke['nam_hoc'][$nam_hoc] = [
                'tong_so_hoc_phan' => 0,
                'tong_so_giang_vien' => 0,
                'tong_so_gio' => 0,
                'hoc_ki' => []
            ];
        }

        if (!isset($thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki])) {
            $thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki] = [
                'tong_so_hoc_phan' => 0,
                'tong_so_giang_vien' => 0,
                'tong_so_gio' => 0,
                'khoa' => []
            ];
        }

        if (!isset($thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id])) {
            $thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id] = [
                'ten' => $khoa->ten,
                'tong_so_hoc_phan' => 0,
                'tong_so_giang_vien' => 0,
                'tong_so_gio' => 0,
                'bo_mon' => []
            ];
        }

        if (!isset($thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['bo_mon'][$bo_mon->id])) {
            $thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['bo_mon'][$bo_mon->id] = [
                'ten' => $bo_mon->ten,
                'tong_so_hoc_phan' => 0,
                'tong_so_giang_vien' => 0,
                'tong_so_gio' => 0,
                'hoc_phan' => []
            ];
        }

        // Thêm thông tin học phần
        $hp_index = array_search($hoc_phan->id, array_column(
            $thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['bo_mon'][$bo_mon->id]['hoc_phan'],
            'id'
        ));

        if ($hp_index === false) {
            $thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['bo_mon'][$bo_mon->id]['hoc_phan'][] = [
                'id' => $hoc_phan->id,
                'ma_hoc_phan' => $hoc_phan->id,
                'ten' => $hoc_phan->ten,
                'so_tin_chi' => $hoc_phan->so_tin_chi,
                'trang_thai' => $ct->trang_thai,
                'thoi_gian_bat_dau' => $ct->thoi_gian_bat_dau,
                'thoi_gian_ket_thuc' => $ct->thoi_gian_ket_thuc,
                'giang_vien' => []
            ];

            // Cập nhật số lượng học phần
            $thongke['nam_hoc'][$nam_hoc]['tong_so_hoc_phan']++;
            $thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['tong_so_hoc_phan']++;
            $thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['tong_so_hoc_phan']++;
            $thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['bo_mon'][$bo_mon->id]['tong_so_hoc_phan']++;
        }

        // Cập nhật thông tin giảng viên và số giờ
        if ($ct->dsGVBienSoans) {
            foreach ($ct->dsGVBienSoans as $gvbs) {
                if (!$gvbs->vienChuc) continue;

                $gv = $gvbs->vienChuc;
                $so_gio = $gvbs->so_gio ?? 0;

                $thongke['nam_hoc'][$nam_hoc]['tong_so_gio'] += $so_gio;
                $thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['tong_so_gio'] += $so_gio;
                $thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['tong_so_gio'] += $so_gio;
                $thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['bo_mon'][$bo_mon->id]['tong_so_gio'] += $so_gio;
                $thongke['tong_hop']['tong_so_gio'] += $so_gio;

                $thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['bo_mon'][$bo_mon->id]['hoc_phan'][count($thongke['nam_hoc'][$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa->id]['bo_mon'][$bo_mon->id]['hoc_phan']) - 1]['giang_vien'][] = [
                    'id' => $gv->id,
                    'ten' => $gv->name,
                    'so_gio' => $so_gio
                ];
            }
        }
    }

    public function exportExcel(Request $request)
    {
        $khoa_id = $request->input('khoa_id');
        $bomon_id = $request->input('bomon_id');
        $nam_hoc = $request->input('nam_hoc');
        $hoc_ki = $request->input('hoc_ki');
        $bac_dao_tao = $request->input('bac_dao_tao');
        
        // Lấy dữ liệu thống kê
        $thongke_data = $this->layDuLieuThongKe($khoa_id, $bomon_id, $hoc_ki, $nam_hoc, $bac_dao_tao);
        
        // Tạo tên file Excel
        $filename = 'thong_ke_hoc_phan';
        if ($nam_hoc) $filename .= "_nam_{$nam_hoc}";
        if ($hoc_ki) $filename .= "_hk_{$hoc_ki}";
        $filename .= '.xlsx';
        
        // Xuất Excel
        return Excel::download(new ThongKeHocPhanExport($thongke_data), $filename);
    }
} 