<?php

namespace App\Http\Controllers\QualityOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DSDangKy;
use App\Models\CTDSDangKy;
use App\Models\Khoa;
use App\Models\BoMon;
use App\Models\HocPhan;
use App\Models\User;
use App\Models\BienBanHop;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ThongKeExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ThongKeController extends Controller
{
    /**
     * Hiển thị trang thống kê
     */
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
        // Lấy danh sách các khoa, bộ môn, năm học, học kỳ để hiển thị trong bộ lọc
        $khoas = Khoa::where('able', true)->where('id', '!=', 'admin')->where('id', '!=', 'DBCL')->get();
        
        $bomons = BoMon::with('khoa')
            ->where('able', true)
            ->get();
            
        // Lấy danh sách các học kỳ từ hệ thống
        $ds_hoc_ki = ['1', '2', 'Hè'];
        
        // Lấy danh sách năm học từ dữ liệu hiện có
        $ds_nam_hoc = DSDangKy::select('nam_hoc')
            ->distinct()
            ->pluck('nam_hoc')
            ->toArray();
            
        // Xử lý các tham số lọc
        $khoa_id = $request->input('khoa_id');
        $bomon_id = $request->input('bomon_id');
        $nam_hoc = $request->input('nam_hoc');
        $hoc_ki = $request->input('hoc_ki');
        
        // Lấy dữ liệu thống kê tổng hợp
        $thongke_data = $this->layDuLieuThongKe($khoa_id, $bomon_id, $nam_hoc, $hoc_ki);

        // Trả về view với dữ liệu
        return Inertia::render('QualityOffice/ThongKe/Index', [
            'role' => $role,
            'khoas' => $khoas,
            'bomons' => $bomons,
            'ds_hoc_ki' => $ds_hoc_ki,
            'ds_nam_hoc' => $ds_nam_hoc,
            'filters' => [
                'khoa_id' => $khoa_id,
                'bomon_id' => $bomon_id,
                'nam_hoc' => $nam_hoc,
                'hoc_ki' => $hoc_ki,
            ],
            'thongke_data' => $thongke_data
        ]);
    }
    
    /**
     * Lấy dữ liệu thống kê theo các tiêu chí lọc
     */
    private function layDuLieuThongKe($khoa_id, $bomon_id, $nam_hoc, $hoc_ki)
    {
        // Tạo query cơ bản
        $query = DSDangKy::with([
                'boMon',
                'boMon.khoa',
                'ctDSDangKies',
                'ctDSDangKies.hocPhan',
                'ctDSDangKies.dsGVBienSoans.vienChuc',
                'ctDSDangKies.bienBanHop.dsHop.vienChuc'
            ])
            ->where('able', true);
            
        // Áp dụng các điều kiện lọc nếu có
        if ($khoa_id) {
            $query->whereHas('boMon', function($q) use ($khoa_id) {
                $q->where('id_khoa', $khoa_id);
            });
        }
        
        if ($bomon_id) {
            $query->where('id_bo_mon', $bomon_id);
        }
        
        if ($nam_hoc) {
            $query->where('nam_hoc', $nam_hoc);
        }
        
        if ($hoc_ki) {
            $query->where('hoc_ki', $hoc_ki);
        }
        
        // Lấy dữ liệu
        $dsdangky = $query->get();
        
        // Tổ chức dữ liệu theo cấu trúc yêu cầu: Năm học -> Học kỳ -> Khoa -> Bộ môn -> Chi tiết
        $thongke = [];
        
        // Biến thống kê tổng hợp
        $tong_hop = [
            'tong_so_gio' => 0,
            'tong_so_nguoi_tham_gia' => 0,
            'tong_so_cau_hoi' => 0,
            'tong_so_de_thi' => 0,
            'tong_so_hoc_phan' => 0
        ];

        // Mảng lưu ID học phần cho từng cấp
        $ds_id_hoc_phan_tong_hop = [];
        $ds_id_hoc_phan_nam = [];
        $ds_id_hoc_phan_hoc_ki = [];
        $ds_id_hoc_phan_khoa = [];
        $ds_id_hoc_phan_bomon = [];
        
        // Mảng lưu ID người tham gia cho từng cấp
        $ds_id_tham_gia_tong_hop = [];
        $ds_id_tham_gia_nam = [];
        $ds_id_tham_gia_hoc_ki = [];
        $ds_id_tham_gia_khoa = [];
        $ds_id_tham_gia_bomon = [];
        
        foreach ($dsdangky as $ds) {
            $nam_hoc = $ds->nam_hoc;
            $hoc_ki = $ds->hoc_ki;
            $khoa_name = $ds->boMon->khoa->ten;
            $khoa_id = $ds->boMon->khoa->id;
            $bomon_name = $ds->boMon->ten;
            $bomon_id = $ds->boMon->id;
            
            // Khởi tạo cấu trúc dữ liệu nếu chưa có
            if (!isset($thongke[$nam_hoc])) {
                $thongke[$nam_hoc] = [
                    'ten' => $nam_hoc,
                    'hoc_ki' => [],
                    'tong_hop' => [
                        'tong_so_gio' => 0,
                        'tong_so_nguoi_tham_gia' => 0,
                        'tong_so_cau_hoi' => 0,
                        'tong_so_de_thi' => 0,
                        'tong_so_hoc_phan' => 0
                    ]
                ];
                $ds_id_tham_gia_nam[$nam_hoc] = [];
                $ds_id_hoc_phan_nam[$nam_hoc] = [];
            }
            
            if (!isset($thongke[$nam_hoc]['hoc_ki'][$hoc_ki])) {
                $thongke[$nam_hoc]['hoc_ki'][$hoc_ki] = [
                    'ten' => 'Học kỳ ' . $hoc_ki,
                    'khoa' => [],
                    'tong_hop' => [
                        'tong_so_gio' => 0,
                        'tong_so_nguoi_tham_gia' => 0,
                        'tong_so_cau_hoi' => 0,
                        'tong_so_de_thi' => 0,
                        'tong_so_hoc_phan' => 0
                    ]
                ];
                $ds_id_tham_gia_hoc_ki["{$nam_hoc}_{$hoc_ki}"] = [];
                $ds_id_hoc_phan_hoc_ki["{$nam_hoc}_{$hoc_ki}"] = [];
            }
            
            if (!isset($thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id])) {
                $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id] = [
                    'ten' => $khoa_name,
                    'id' => $khoa_id,
                    'bomon' => [],
                    'tong_hop' => [
                        'tong_so_gio' => 0,
                        'tong_so_nguoi_tham_gia' => 0,
                        'tong_so_cau_hoi' => 0,
                        'tong_so_de_thi' => 0,
                        'tong_so_hoc_phan' => 0
                    ]
                ];
                $ds_id_tham_gia_khoa["{$nam_hoc}_{$hoc_ki}_{$khoa_id}"] = [];
                $ds_id_hoc_phan_khoa["{$nam_hoc}_{$hoc_ki}_{$khoa_id}"] = [];
            }
            
            if (!isset($thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['bomon'][$bomon_id])) {
                $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['bomon'][$bomon_id] = [
                    'ten' => $bomon_name,
                    'id' => $bomon_id,
                    'chitiet' => [],
                    'tong_hop' => [
                        'tong_so_gio' => 0,
                        'tong_so_nguoi_tham_gia' => 0,
                        'tong_so_cau_hoi' => 0,
                        'tong_so_de_thi' => 0,
                        'tong_so_hoc_phan' => 0
                    ]
                ];
                $ds_id_tham_gia_bomon["{$nam_hoc}_{$hoc_ki}_{$khoa_id}_{$bomon_id}"] = [];
                $ds_id_hoc_phan_bomon["{$nam_hoc}_{$hoc_ki}_{$khoa_id}_{$bomon_id}"] = [];
            }
            
            // Thêm chi tiết đăng ký vào cấu trúc
            foreach ($ds->ctDSDangKies as $ct) {
                if ($ct->able) {
                    // Lấy thông tin người phản biện từ biên bản họp
                    $nguoi_phan_bien = BienBanHop::where('id_ct_ds_dang_ky', $ct->id)
                        ->where('able', true)
                        ->with('dsHop.vienChuc')
                        ->get();
                    
                    // Tính tổng số giờ của người phản biện và lấy danh sách người phản biện
                    $tong_gio_phan_bien = 0;
                    $ds_nguoi_phan_bien = [];
                    $ds_id_phan_bien = [];
                    
                    foreach ($nguoi_phan_bien as $bb) {
                        foreach ($bb->dsHop as $tg) {
                            $gio_phan_bien = isset($tg->so_gio) ? $tg->so_gio : 0;
                            $tong_gio_phan_bien += $gio_phan_bien;
                            
                            // Thêm tên người phản biện kèm số giờ
                            $ds_nguoi_phan_bien[] = $tg->vienChuc->name . ' (' . $gio_phan_bien . ')';
                            $ds_id_phan_bien[] = $tg->vienChuc->id;
                        }
                    }
                    
                    // Lấy danh sách giảng viên biên soạn
                    $tong_gio_bien_soan = 0;
                    $ds_id_giang_vien = [];
                    $ds_giang_vien = $ct->dsGVBienSoans->map(function($gvbs) use (&$tong_gio_bien_soan, &$ds_id_giang_vien) {
                        $gio = $gvbs->so_gio ?? 0;
                        $tong_gio_bien_soan += $gio;
                        $ds_id_giang_vien[] = $gvbs->vienChuc->id;
                        return $gvbs->vienChuc->name . ' (' . $gio . ')';
                    })->join(', ');
                    
                    // Tổng số giờ = số giờ biên soạn + số giờ phản biện
                    $tong_gio = $tong_gio_bien_soan + $tong_gio_phan_bien;
                    
                    // Chuỗi tên người phản biện
                    $nguoi_phan_bien_str = implode(', ', $ds_nguoi_phan_bien);
                    
                    // Tính số người tham gia không trùng lặp cho từng cấp
                    $ds_id_tham_gia = array_unique(array_merge($ds_id_giang_vien, $ds_id_phan_bien));
                    
                    // Cập nhật danh sách ID người tham gia cho từng cấp
                    $ds_id_tham_gia_tong_hop = array_unique(array_merge($ds_id_tham_gia_tong_hop, $ds_id_tham_gia));
                    $ds_id_tham_gia_nam[$nam_hoc] = array_unique(array_merge($ds_id_tham_gia_nam[$nam_hoc], $ds_id_tham_gia));
                    $ds_id_tham_gia_hoc_ki["{$nam_hoc}_{$hoc_ki}"] = array_unique(array_merge($ds_id_tham_gia_hoc_ki["{$nam_hoc}_{$hoc_ki}"], $ds_id_tham_gia));
                    $ds_id_tham_gia_khoa["{$nam_hoc}_{$hoc_ki}_{$khoa_id}"] = array_unique(array_merge($ds_id_tham_gia_khoa["{$nam_hoc}_{$hoc_ki}_{$khoa_id}"], $ds_id_tham_gia));
                    $ds_id_tham_gia_bomon["{$nam_hoc}_{$hoc_ki}_{$khoa_id}_{$bomon_id}"] = array_unique(array_merge($ds_id_tham_gia_bomon["{$nam_hoc}_{$hoc_ki}_{$khoa_id}_{$bomon_id}"], $ds_id_tham_gia));
                    
                    // Cập nhật danh sách ID học phần cho từng cấp
                    $ds_id_hoc_phan_tong_hop[] = $ct->id_hoc_phan;
                    $ds_id_hoc_phan_nam[$nam_hoc][] = $ct->id_hoc_phan;
                    $ds_id_hoc_phan_hoc_ki["{$nam_hoc}_{$hoc_ki}"][] = $ct->id_hoc_phan;
                    $ds_id_hoc_phan_khoa["{$nam_hoc}_{$hoc_ki}_{$khoa_id}"][] = $ct->id_hoc_phan;
                    $ds_id_hoc_phan_bomon["{$nam_hoc}_{$hoc_ki}_{$khoa_id}_{$bomon_id}"][] = $ct->id_hoc_phan;
                    
                    // Cập nhật thống kê tổng hợp bộ môn
                    $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['bomon'][$bomon_id]['tong_hop']['tong_so_gio'] += $tong_gio;
                    $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['bomon'][$bomon_id]['tong_hop']['tong_so_nguoi_tham_gia'] = count($ds_id_tham_gia_bomon["{$nam_hoc}_{$hoc_ki}_{$khoa_id}_{$bomon_id}"]);
                    $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['bomon'][$bomon_id]['tong_hop']['tong_so_hoc_phan'] = count(array_unique($ds_id_hoc_phan_bomon["{$nam_hoc}_{$hoc_ki}_{$khoa_id}_{$bomon_id}"]));
                    
                    if ($ct->loai_ngan_hang == 1) {
                        $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['bomon'][$bomon_id]['tong_hop']['tong_so_cau_hoi'] += $ct->so_luong;
                    } else {
                        $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['bomon'][$bomon_id]['tong_hop']['tong_so_de_thi'] += $ct->so_luong;
                    }
                    
                    // Cập nhật thống kê cấp khoa
                    $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['tong_hop']['tong_so_gio'] += $tong_gio;
                    $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['tong_hop']['tong_so_nguoi_tham_gia'] = count($ds_id_tham_gia_khoa["{$nam_hoc}_{$hoc_ki}_{$khoa_id}"]);
                    $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['tong_hop']['tong_so_hoc_phan'] = count(array_unique($ds_id_hoc_phan_khoa["{$nam_hoc}_{$hoc_ki}_{$khoa_id}"]));
                    
                    if ($ct->loai_ngan_hang == 1) {
                        $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['tong_hop']['tong_so_cau_hoi'] += $ct->so_luong;
                    } else {
                        $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['tong_hop']['tong_so_de_thi'] += $ct->so_luong;
                    }
                    
                    // Cập nhật thống kê cấp học kỳ
                    $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['tong_hop']['tong_so_gio'] += $tong_gio;
                    $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['tong_hop']['tong_so_nguoi_tham_gia'] = count($ds_id_tham_gia_hoc_ki["{$nam_hoc}_{$hoc_ki}"]);
                    $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['tong_hop']['tong_so_hoc_phan'] = count(array_unique($ds_id_hoc_phan_hoc_ki["{$nam_hoc}_{$hoc_ki}"]));
                    
                    if ($ct->loai_ngan_hang == 1) {
                        $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['tong_hop']['tong_so_cau_hoi'] += $ct->so_luong;
                    } else {
                        $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['tong_hop']['tong_so_de_thi'] += $ct->so_luong;
                    }
                    
                    // Cập nhật thống kê cấp năm học
                    $thongke[$nam_hoc]['tong_hop']['tong_so_gio'] += $tong_gio;
                    $thongke[$nam_hoc]['tong_hop']['tong_so_nguoi_tham_gia'] = count($ds_id_tham_gia_nam[$nam_hoc]);
                    $thongke[$nam_hoc]['tong_hop']['tong_so_hoc_phan'] = count(array_unique($ds_id_hoc_phan_nam[$nam_hoc]));
                    
                    if ($ct->loai_ngan_hang == 1) {
                        $thongke[$nam_hoc]['tong_hop']['tong_so_cau_hoi'] += $ct->so_luong;
                    } else {
                        $thongke[$nam_hoc]['tong_hop']['tong_so_de_thi'] += $ct->so_luong;
                    }
                    
                    // Cập nhật thống kê tổng hợp chung
                    $tong_hop['tong_so_gio'] += $tong_gio;
                    $tong_hop['tong_so_nguoi_tham_gia'] = count($ds_id_tham_gia_tong_hop);
                    $tong_hop['tong_so_hoc_phan'] = count(array_unique($ds_id_hoc_phan_tong_hop));
                    
                    if ($ct->loai_ngan_hang == 1) {
                        $tong_hop['tong_so_cau_hoi'] += $ct->so_luong;
                    } else {
                        $tong_hop['tong_so_de_thi'] += $ct->so_luong;
                    }
                        
                    $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['bomon'][$bomon_id]['chitiet'][] = [
                        'id' => $ct->id,
                        'hoc_phan' => $ct->hocPhan->ten,
                        'giang_vien' => $ds_giang_vien,
                        'nguoi_phan_bien' => $nguoi_phan_bien_str,
                        'so_gio' => $tong_gio,
                        'hinh_thuc_thi' => $ct->hinh_thuc_thi,
                        'loai_ngan_hang' => $ct->loai_ngan_hang == 1 ? 'Ngân hàng câu hỏi' : 'Ngân hàng đề thi',
                        'so_luong' => $ct->so_luong,
                        'trang_thai' => $ct->trang_thai
                    ];
                }
            }
        }
        
        // Sắp xếp dữ liệu
        ksort($thongke); // Sắp xếp năm học
        foreach ($thongke as &$nam) {
            ksort($nam['hoc_ki']); // Sắp xếp học kỳ
            
            foreach ($nam['hoc_ki'] as &$hk) {
                ksort($hk['khoa']); // Sắp xếp khoa
                
                foreach ($hk['khoa'] as &$k) {
                    ksort($k['bomon']); // Sắp xếp bộ môn
                }
            }
        }
        
        // Thêm thống kê tổng hợp vào kết quả
        $thongke['tong_hop'] = $tong_hop;
        
        return $thongke;
    }
    
    /**
     * Xuất dữ liệu thống kê ra file Excel
     */
    public function exportExcel(Request $request)
    {
        $khoa_id = $request->input('khoa_id');
        $bomon_id = $request->input('bomon_id');
        $nam_hoc = $request->input('nam_hoc');
        $hoc_ki = $request->input('hoc_ki');
        
        // Lấy dữ liệu thống kê
        $thongke_data = $this->layDuLieuThongKe($khoa_id, $bomon_id, $nam_hoc, $hoc_ki);
        
        // Tạo tên file Excel
        $filename = 'thong_ke_bien_soan';
        if ($nam_hoc) $filename .= "_nam_{$nam_hoc}";
        if ($hoc_ki) $filename .= "_hk_{$hoc_ki}";
        $filename .= '.xlsx';
        
        // Xuất Excel
        return Excel::download(new ThongKeExport($thongke_data), $filename);
    }

    public function exportExcelGioThamGia(Request $request)
    {
        $khoa_id = $request->input('khoa_id');
        $bomon_id = $request->input('bomon_id');
        $nam_hoc = $request->input('nam_hoc');
        $hoc_ki = $request->input('hoc_ki');

        // Lấy tất cả chi tiết đăng ký phù hợp
        $query = CTDSDangKy::with(['dsGVBienSoans.vienChuc', 'bienBanHop.dsHop.vienChuc'])
            ->where('able', true);

        if ($bomon_id) {
            $query->whereHas('dsDangKy', function($q) use ($bomon_id) {
                $q->where('id_bo_mon', $bomon_id);
            });
        } elseif ($khoa_id) {
            $query->whereHas('dsDangKy.boMon', function($q) use ($khoa_id) {
                $q->where('id_khoa', $khoa_id);
            });
        }
        if ($nam_hoc) $query->whereHas('dsDangKy', fn($q) => $q->where('nam_hoc', $nam_hoc));
        if ($hoc_ki) $query->whereHas('dsDangKy', fn($q) => $q->where('hoc_ki', $hoc_ki));

        $ctds = $query->get();

        // Gom nhóm theo viên chức
        $data = [];
        foreach ($ctds as $ct) {
            // Giảng viên biên soạn
            foreach ($ct->dsGVBienSoans as $gvbs) {
                $id = $gvbs->vienChuc->id;
                if (!isset($data[$id])) {
                    $data[$id] = [
                        'ma_vien_chuc' => $gvbs->vienChuc->ma_vien_chuc ?? $gvbs->vienChuc->id,
                        'ten' => $gvbs->vienChuc->name,
                        'gio_bien_soan' => 0,
                        'gio_phan_bien' => 0,
                        'tong_gio' => 0,
                    ];
                }
                $data[$id]['gio_bien_soan'] += $gvbs->so_gio ?? 0;
            }
            // Người phản biện
            foreach ($ct->bienBanHop as $bb) {
                foreach ($bb->dsHop as $hop) {
                    $id = $hop->vienChuc->id;
                    if (!isset($data[$id])) {
                        $data[$id] = [
                            'ma_vien_chuc' => $hop->vienChuc->ma_vien_chuc ?? $hop->vienChuc->id,
                            'ten' => $hop->vienChuc->name,
                            'gio_bien_soan' => 0,
                            'gio_phan_bien' => 0,
                            'tong_gio' => 0,
                        ];
                    }
                    $data[$id]['gio_phan_bien'] += $hop->so_gio ?? 0;
                }
            }
        }
        // Tổng giờ
        foreach ($data as &$row) {
            $row['tong_gio'] = $row['gio_bien_soan'] + $row['gio_phan_bien'];
        }

        // Xuất excel bằng ThongKeExport (tái sử dụng cấu trúc headings, collection)
        $exportData = collect($data)->values();
        $headings = [
            'Mã viên chức', 'Tên', 'Tổng giờ biên soạn', 'Tổng giờ phản biện', 'Tổng giờ tham gia'
        ];
        $filename = 'thong_ke_gio_tham_gia.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ThongKeExportSimple($exportData, $headings), $filename);
    }
} 