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
use App\Exports\ThongKeGiangVienExport;
use App\Exports\ThongKeBarChartExport;
use App\Exports\ThongKePieChartExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ThongKeController extends Controller
{
    /**
     * Hiển thị trang thống kê tổng hợp
     */
    public function index(Request $request)
    {
        $role = $this->getUserRole();
        
        // Lấy danh sách bộ lọc
        $filters = $this->getFilterOptions();
        
        // Lấy dữ liệu thống kê
        $thongKeData = $this->layDuLieuThongKe($request);
        
        // Tạo dữ liệu biểu đồ
        $chartData = $this->taoDataBieuDo($thongKeData, $request);
        
        return Inertia::render('QualityOffice/ThongKe/Index', [
            'role' => $role,
            'filters' => $filters,
            'currentFilters' => $request->only(['khoa_id', 'bomon_id', 'nam_hoc', 'hoc_ki']),
            'thongKeData' => $thongKeData,
            'chartData' => $chartData
        ]);
    }
    
    /**
     * Lấy role của user hiện tại
     */
    private function getUserRole()
    {
        $roles = Auth::user()->getRoleNames();
        
        if ($roles->contains('Nhân viên P.ĐBCL')) {
            return 'dbcl';
        } elseif ($roles->contains('Admin')) {
            return 'admin';
        }
        
        return 'user';
    }
    
    /**
     * Lấy các tùy chọn bộ lọc
     */
    private function getFilterOptions()
    {
        return [
            'khoas' => Khoa::where('able', true)
                ->whereNotIn('id', ['admin', 'DBCL'])
                ->get(),
            'bomons' => BoMon::with('khoa')
                ->where('able', true)
                ->whereHas('khoa', function($q) {
                    $q->where('able', true)
                      ->whereNotIn('id', ['admin', 'DBCL']);
                })
                ->get(),
            'ds_nam_hoc' => DSDangKy::where('able', true)
                ->distinct()
                ->pluck('nam_hoc')
                ->sort()
                ->values(),
            'ds_hoc_ki' => ['1', '2', 'Hè']
        ];
    }
    
    /**
     * Lấy dữ liệu thống kê chính
     */
    private function layDuLieuThongKe(Request $request)
    {
        $khoa_id = $request->input('khoa_id');
        $bomon_id = $request->input('bomon_id');
        $nam_hoc = $request->input('nam_hoc');
        $hoc_ki = $request->input('hoc_ki');
        
        // Query cơ bản
        $query = CTDSDangKy::with([
            'hocPhan',
            'dsGVBienSoans.vienChuc',
            'bienBanHop.dsHop.vienChuc',
            'dsDangKy.boMon.khoa'
        ])->where('able', true);
        
        // Áp dụng bộ lọc
        if ($bomon_id) {
            $query->whereHas('dsDangKy', function($q) use ($bomon_id) {
                $q->where('id_bo_mon', $bomon_id)->where('able', true);
            });
        } elseif ($khoa_id) {
            $query->whereHas('dsDangKy.boMon', function($q) use ($khoa_id) {
                $q->where('id_khoa', $khoa_id)->where('able', true);
            });
        }
        
        if ($nam_hoc) {
            $query->whereHas('dsDangKy', function($q) use ($nam_hoc) {
                $q->where('nam_hoc', $nam_hoc)->where('able', true);
            });
        }
        
        if ($hoc_ki) {
            $query->whereHas('dsDangKy', function($q) use ($hoc_ki) {
                $q->where('hoc_ki', $hoc_ki)->where('able', true);
            });
        }
        
        $ctDangKies = $query->get();
        
        // Xử lý dữ liệu
        return $this->xuLyDuLieuThongKe($ctDangKies);
    }
    
    /**
     * Xử lý dữ liệu thống kê
     */
    private function xuLyDuLieuThongKe($ctDangKies)
    {
        $result = [
            'tong_quan' => [
                'tong_hoc_phan' => 0,
                'tong_vien_chuc' => 0,
                'tong_gio' => 0
            ],
            'chi_tiet_khoa' => [],
            'chi_tiet_vien_chuc' => []
        ];
        
        $dsHocPhan = [];
        $dsVienChuc = [];
        $chiTietVienChuc = [];
        
        foreach ($ctDangKies as $ct) {
            $khoa = $ct->dsDangKy->boMon->khoa;
            $boMon = $ct->dsDangKy->boMon;
            $hocPhan = $ct->hocPhan;
            
            // Đếm học phần
            $dsHocPhan[$hocPhan->id] = $hocPhan;
            
            // Khởi tạo cấu trúc khoa
            if (!isset($result['chi_tiet_khoa'][$khoa->id])) {
                $result['chi_tiet_khoa'][$khoa->id] = [
                    'ten' => $khoa->ten,
                    'tong_hoc_phan' => 0,
                    'tong_vien_chuc' => 0,
                    'tong_gio' => 0,
                    'bo_mon' => []
                ];
            }
            
            // Khởi tạo cấu trúc bộ môn
            if (!isset($result['chi_tiet_khoa'][$khoa->id]['bo_mon'][$boMon->id])) {
                $result['chi_tiet_khoa'][$khoa->id]['bo_mon'][$boMon->id] = [
                    'ten' => $boMon->ten,
                    'tong_hoc_phan' => 0,
                    'tong_vien_chuc' => 0,
                    'tong_gio' => 0,
                    'hoc_phan' => []
                ];
            }
            
            // Thêm học phần vào bộ môn
            $result['chi_tiet_khoa'][$khoa->id]['bo_mon'][$boMon->id]['hoc_phan'][$hocPhan->id] = [
                'ma' => $hocPhan->id,
                'ten' => $hocPhan->ten,
                'so_tin_chi' => $hocPhan->so_tin_chi,
                'trang_thai' => $ct->trang_thai,
                'vien_chuc' => []
            ];
            
            // Xử lý giảng viên biên soạn
            foreach ($ct->dsGVBienSoans as $gvbs) {
                $vienChuc = $gvbs->vienChuc;
                $soGio = $gvbs->so_gio ?? 0;
                
                $this->capNhatThongTinVienChuc(
                    $dsVienChuc, 
                    $chiTietVienChuc, 
                    $result, 
                    $vienChuc, 
                    $hocPhan, 
                    $khoa, 
                    $boMon, 
                    $soGio, 
                    'Biên soạn'
                );
            }
            
            // Xử lý người phản biện
            foreach ($ct->bienBanHop as $bb) {
                foreach ($bb->dsHop as $hop) {
                    $vienChuc = $hop->vienChuc;
                    $soGio = $hop->so_gio ?? 0;
                    
                    $this->capNhatThongTinVienChuc(
                        $dsVienChuc, 
                        $chiTietVienChuc, 
                        $result, 
                        $vienChuc, 
                        $hocPhan, 
                        $khoa, 
                        $boMon, 
                        $soGio, 
                        'Phản biện'
                    );
                }
            }
        }
        
        // Cập nhật tổng quan
        $result['tong_quan']['tong_hoc_phan'] = count($dsHocPhan);
        $result['tong_quan']['tong_vien_chuc'] = count($dsVienChuc);
        $result['tong_quan']['tong_gio'] = array_sum(array_column($chiTietVienChuc, 'tong_gio'));
        
        // Cập nhật chi tiết viên chức
        $result['chi_tiet_vien_chuc'] = array_values($chiTietVienChuc);
        
        // Cập nhật thống kê theo khoa/bộ môn
        $this->capNhatThongKeKhoa($result);
        
        return $result;
    }
    
    /**
     * Cập nhật thông tin viên chức
     */
    private function capNhatThongTinVienChuc(&$dsVienChuc, &$chiTietVienChuc, &$result, $vienChuc, $hocPhan, $khoa, $boMon, $soGio, $loai)
    {
        $dsVienChuc[$vienChuc->id] = $vienChuc;
        
        // Khởi tạo chi tiết viên chức
        if (!isset($chiTietVienChuc[$vienChuc->id])) {
            $chiTietVienChuc[$vienChuc->id] = [
                'id' => $vienChuc->id,
                'ma' => $vienChuc->ma_vien_chuc ?? $vienChuc->id,
                'ten' => $vienChuc->name,
                'tong_gio' => 0,
                'chi_tiet_hoc_phan' => []
            ];
        }
        
        // Cập nhật giờ
        $chiTietVienChuc[$vienChuc->id]['tong_gio'] += $soGio;
        
        // Thêm chi tiết học phần
        $key = $hocPhan->id . '_' . $loai;
        if (!isset($chiTietVienChuc[$vienChuc->id]['chi_tiet_hoc_phan'][$key])) {
            $chiTietVienChuc[$vienChuc->id]['chi_tiet_hoc_phan'][$key] = [
                'ma_hoc_phan' => $hocPhan->id,
                'ten_hoc_phan' => $hocPhan->ten,
                'khoa' => $khoa->ten,
                'bo_mon' => $boMon->ten,
                'loai' => $loai,
                'so_gio' => 0
            ];
        }
        
        $chiTietVienChuc[$vienChuc->id]['chi_tiet_hoc_phan'][$key]['so_gio'] += $soGio;
        
        // Cập nhật vào cấu trúc học phần
        $result['chi_tiet_khoa'][$khoa->id]['bo_mon'][$boMon->id]['hoc_phan'][$hocPhan->id]['vien_chuc'][] = [
            'id' => $vienChuc->id,
            'ten' => $vienChuc->name,
            'loai' => $loai,
            'so_gio' => $soGio
        ];
    }
    
    /**
     * Cập nhật thống kê theo khoa/bộ môn
     */
    private function capNhatThongKeKhoa(&$result)
    {
        foreach ($result['chi_tiet_khoa'] as $khoaId => &$khoa) {
            $dsVienChucKhoa = [];
            $tongGioKhoa = 0;
            
            foreach ($khoa['bo_mon'] as $boMonId => &$boMon) {
                $dsVienChucBoMon = [];
                $tongGioBoMon = 0;
                
                $boMon['tong_hoc_phan'] = count($boMon['hoc_phan']);
                
                foreach ($boMon['hoc_phan'] as &$hocPhan) {
                    foreach ($hocPhan['vien_chuc'] as $vc) {
                        $dsVienChucBoMon[$vc['id']] = true;
                        $dsVienChucKhoa[$vc['id']] = true;
                        $tongGioBoMon += $vc['so_gio'];
                        $tongGioKhoa += $vc['so_gio'];
                    }
                }
                
                $boMon['tong_vien_chuc'] = count($dsVienChucBoMon);
                $boMon['tong_gio'] = $tongGioBoMon;
            }
            
            $khoa['tong_hoc_phan'] = array_sum(array_column($khoa['bo_mon'], 'tong_hoc_phan'));
            $khoa['tong_vien_chuc'] = count($dsVienChucKhoa);
            $khoa['tong_gio'] = $tongGioKhoa;
        }
    }
    
    /**
     * Tạo dữ liệu biểu đồ
     */
    private function taoDataBieuDo($thongKeData, Request $request)
    {
        $khoa_id = $request->input('khoa_id');
        $bomon_id = $request->input('bomon_id');
        
        $chartData = [
            'pie_chart' => [],
            'bar_chart' => [],
            'line_chart' => []
        ];
        
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
            '#9966FF', '#FF9F40', '#FF6B6B', '#4ECDC4',
            '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD',
            '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9'
        ];
        
        if (!$khoa_id) {
            // Biểu đồ theo khoa
            $colorIndex = 0;
            foreach ($thongKeData['chi_tiet_khoa'] as $khoa) {
                $chartData['pie_chart'][] = [
                    'label' => $khoa['ten'],
                    'value' => $khoa['tong_hoc_phan'],
                    'color' => $colors[$colorIndex % count($colors)]
                ];
                
                $chartData['bar_chart'][] = [
                    'name' => $khoa['ten'],
                    'hoc_phan' => $khoa['tong_hoc_phan'],
                    'vien_chuc' => $khoa['tong_vien_chuc'],
                    'gio' => $khoa['tong_gio']
                ];
                $colorIndex++;
            }
        } elseif (!$bomon_id) {
            // Biểu đồ theo bộ môn của khoa
            $khoa = $thongKeData['chi_tiet_khoa'][$khoa_id] ?? null;
            if ($khoa) {
                $colorIndex = 0;
                foreach ($khoa['bo_mon'] as $boMon) {
                    $chartData['pie_chart'][] = [
                        'label' => $boMon['ten'],
                        'value' => $boMon['tong_hoc_phan'],
                        'color' => $colors[$colorIndex % count($colors)]
                    ];
                    
                    $chartData['bar_chart'][] = [
                        'name' => $boMon['ten'],
                        'hoc_phan' => $boMon['tong_hoc_phan'],
                        'vien_chuc' => $boMon['tong_vien_chuc'],
                        'gio' => $boMon['tong_gio']
                    ];
                    $colorIndex++;
                }
            }
        } else {
            // Biểu đồ theo học phần của bộ môn
            $khoa = $thongKeData['chi_tiet_khoa'][$khoa_id] ?? null;
            if ($khoa && isset($khoa['bo_mon'][$bomon_id])) {
                $boMon = $khoa['bo_mon'][$bomon_id];
                $colorIndex = 0;
                foreach ($boMon['hoc_phan'] as $hocPhan) {
                    $chartData['pie_chart'][] = [
                        'label' => $hocPhan['ten'],
                        'value' => 1, // Mỗi học phần = 1
                        'color' => $colors[$colorIndex % count($colors)]
                    ];
                    
                    $chartData['bar_chart'][] = [
                        'name' => $hocPhan['ten'],
                        'hoc_phan' => 1,
                        'vien_chuc' => count($hocPhan['vien_chuc']),
                        'gio' => array_sum(array_column($hocPhan['vien_chuc'], 'so_gio'))
                    ];
                    $colorIndex++;
                }
            }
        }
        
        return $chartData;
    }
    
    /**
     * Xuất Excel
     */
    public function exportExcel(Request $request)
    {
        $thongKeData = $this->layDuLieuThongKe($request);
        
        $filename = 'thong_ke_tong_hop_' . date('Y_m_d_H_i_s') . '.xlsx';
        
        return Excel::download(new ThongKeExport($thongKeData), $filename);
    }
    
    /**
     * Xuất Excel danh sách giảng viên
     */
    public function exportGiangVienExcel(Request $request)
    {
        $thongKeData = $this->layDuLieuThongKe($request);
        $filters = $request->only(['khoa_id', 'bomon_id', 'nam_hoc', 'hoc_ki']);
        
        $filename = 'danh_sach_giang_vien_' . date('Y_m_d_H_i_s') . '.xlsx';
        
        return Excel::download(new ThongKeGiangVienExport($thongKeData, $filters), $filename);
    }
    
    /**
     * Xuất Excel biểu đồ cột
     */
    public function exportBarChartExcel(Request $request)
    {
        $thongKeData = $this->layDuLieuThongKe($request);
        $chartData = $this->taoDataBieuDo($thongKeData, $request);
        $filters = $request->only(['khoa_id', 'bomon_id', 'nam_hoc', 'hoc_ki']);
        
        $filename = 'bieu_do_cot_' . date('Y_m_d_H_i_s') . '.xlsx';
        
        return Excel::download(new ThongKeBarChartExport($thongKeData, $chartData, $filters), $filename);
    }
    
    /**
     * Xuất Excel biểu đồ tròn
     */
    public function exportPieChartExcel(Request $request)
    {
        $thongKeData = $this->layDuLieuThongKe($request);
        $chartData = $this->taoDataBieuDo($thongKeData, $request);
        $filters = $request->only(['khoa_id', 'bomon_id', 'nam_hoc', 'hoc_ki']);
        
        $filename = 'bieu_do_tron_' . date('Y_m_d_H_i_s') . '.xlsx';
        
        return Excel::download(new ThongKePieChartExport($thongKeData, $chartData, $filters), $filename);
    }
} 