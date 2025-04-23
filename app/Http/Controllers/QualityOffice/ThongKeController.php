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

class ThongKeController extends Controller
{
    /**
     * Hiển thị trang thống kê
     */
    public function index(Request $request)
    {
        // Lấy danh sách các khoa, bộ môn, năm học, học kỳ để hiển thị trong bộ lọc
        $khoas = Khoa::where('able', true)->get();
        
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
                'ctDSDangKies.vienChuc'
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
                    'hoc_ki' => []
                ];
            }
            
            if (!isset($thongke[$nam_hoc]['hoc_ki'][$hoc_ki])) {
                $thongke[$nam_hoc]['hoc_ki'][$hoc_ki] = [
                    'ten' => 'Học kỳ ' . $hoc_ki,
                    'khoa' => []
                ];
            }
            
            if (!isset($thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id])) {
                $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id] = [
                    'ten' => $khoa_name,
                    'id' => $khoa_id,
                    'bomon' => []
                ];
            }
            
            if (!isset($thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['bomon'][$bomon_id])) {
                $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['bomon'][$bomon_id] = [
                    'ten' => $bomon_name,
                    'id' => $bomon_id,
                    'chitiet' => []
                ];
            }
            
            // Thêm chi tiết đăng ký vào cấu trúc
            foreach ($ds->ctDSDangKies as $ct) {
                if ($ct->able && $ct->trang_thai == 'Approved') {
                    // Lấy thông tin người phản biện từ biên bản họp
                    $nguoi_phan_bien = BienBanHop::where('id_ct_ds_dang_ky', $ct->id)
                        ->where('able', true)
                        ->with('dsHop.vienChuc')
                        ->get();
                    
                    // Tính tổng số giờ của người phản biện
                    $tong_gio_phan_bien = 0;
                    $ds_nguoi_phan_bien = [];
                    
                    foreach ($nguoi_phan_bien as $bb) {
                        foreach ($bb->dsHop as $tg) {
                            // Giả sử số giờ của người phản biện là field 'so_gio' trong dsHop
                            $gio_phan_bien = isset($tg->so_gio) ? $tg->so_gio : 0;
                            $tong_gio_phan_bien += $gio_phan_bien;
                            
                            // Thêm tên người phản biện kèm số giờ
                            $ds_nguoi_phan_bien[] = $tg->vienChuc->name . ' (' . $gio_phan_bien . ')';
                        }
                    }
                    
                    // Tổng số giờ = số giờ của giảng viên + số giờ của người phản biện
                    $tong_gio = $ct->so_gio + $tong_gio_phan_bien;
                    
                    // Chuỗi tên người phản biện
                    $nguoi_phan_bien_str = implode(', ', $ds_nguoi_phan_bien);
                        
                    $thongke[$nam_hoc]['hoc_ki'][$hoc_ki]['khoa'][$khoa_id]['bomon'][$bomon_id]['chitiet'][] = [
                        'id' => $ct->id,
                        'hoc_phan' => $ct->hocPhan->ten,
                        'giang_vien' => $ct->vienChuc->name . ' (' . $ct->so_gio . ')',
                        'nguoi_phan_bien' => $nguoi_phan_bien_str,
                        'so_gio' => $tong_gio,
                        'hinh_thuc_thi' => $ct->hinh_thuc_thi,
                        'loai_ngan_hang' => $ct->loai_ngan_hang == 1 ? 'Ngân hàng câu hỏi' : 'Ngân hàng đề thi',
                        'so_luong' => $ct->so_luong
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
} 