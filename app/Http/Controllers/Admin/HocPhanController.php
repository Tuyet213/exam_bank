<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HocPhan;
use App\Models\BoMon;
use App\Models\BacDaoTao;
use App\Models\ChuanDauRa;
use App\Models\Chuong;
use App\Models\ChuongChuanDauRa;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\Khoa;
use Illuminate\Support\Facades\Log;
class HocPhanController extends Controller
{
    public function index(Request $request)
    {
        $query = HocPhan::where('able', true)
            ->with(['bomon', 'bacdaotao', 'bomon.khoa', 'chuongs', 'chuongs.chuongChuanDauRa.chuanDauRa']);

        if ($request->has('khoa_id') && !empty($request->input('khoa_id'))) {
            $query->whereHas('bomon', function ($q) use ($request) {
                $q->where('id_khoa', $request->input('khoa_id'));
            });
        }

        if ($request->has('search') && !empty($request->input('search'))) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                  ->orWhere('ten', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('id_bo_mon') && !empty($request->input('id_bo_mon'))) {
            $query->where('id_bo_mon', $request->input('id_bo_mon'));
        }

        if ($request->has('id_bac_dao_tao') && !empty($request->input('id_bac_dao_tao'))) {
            $query->where('id_bac_dao_tao', $request->input('id_bac_dao_tao'));
        }

        $hocphans = $query->paginate(10)->withQueryString();
        
        $hocphans->filters = [
            'search' => $request->input('search'),
            'id_bo_mon' => $request->input('id_bo_mon'),
            'id_bac_dao_tao' => $request->input('id_bac_dao_tao'),
            'khoa_id' => $request->input('khoa_id')
        ];

        $khoas = Khoa::where('able', true)
            ->whereNotIn('id', ['admin', 'DBCL'])
            ->get(['id', 'ten']);
        $bomons = BoMon::where('able', true)->with('khoa')->get(['id', 'ten', 'id_khoa']);
        $bacdaotaos = BacDaoTao::where('able', true)->get(['id', 'ten']);

        return Inertia::render('Admin/HocPhan/Index', compact('hocphans', 'bomons', 'bacdaotaos', 'khoas'));
    }

    public function create()
    {
        $khoas = Khoa::where('able', true)
            ->whereNotIn('id', ['admin', 'DBCL'])
            ->get();
        $bomons = BoMon::where('able', true)
            ->whereHas('khoa', function($query) {
                $query->whereNotIn('id', ['admin', 'DBCL']);
            })
            ->get();
        $bacdaotaos = BacDaoTao::where('able', true)->get();
        
        return Inertia::render('Admin/HocPhan/Create', compact('bomons', 'bacdaotaos', 'khoas'));
    }

    public function store(Request $request)
    {
        Log::info('request', $request->all());
        $validated = $request->validate([
            'id' => 'required|string|max:6|unique:hoc_phans,id',
            'ten' => 'required|string|max:255',
            'id_bo_mon' => 'required|exists:bo_mons,id',
            'id_bac_dao_tao' => 'required|exists:bac_dao_taos,id',
            'so_tin_chi' => 'required|integer|min:0',
            'chuan_dau_ras' => 'array',
            'chuan_dau_ras.*.ten' => 'required|string',
            'chuan_dau_ras.*.noi_dung' => 'required|string',
            'chuongs' => 'array',
            'chuongs.*.ten' => 'required|string',
            'chuongs.*.chuan_dau_ras' => 'array'
        ]);

        // try {
        //     DB::beginTransaction();

            // Tạo học phần
            $hocphan = HocPhan::create($request->only([
                'id', 'ten', 'so_tin_chi', 'id_bo_mon', 'id_bac_dao_tao'
            ]));

            // Tạo chuẩn đầu ra cho học phần
            if ($request->has('chuan_dau_ras')) {
                foreach ($request->chuan_dau_ras as $cdr) {
                    $chuanDauRa = ChuanDauRa::create([
                        'ten' => $cdr['ten'],
                        'noi_dung' => $cdr['noi_dung'],
                        'id_hoc_phan' => (string)$hocphan->id
                    ]);
                }
            }

            // Tạo các chương và liên kết với chuẩn đầu ra
            if ($request->has('chuongs')) {
                foreach ($request->chuongs as $c) {
                    $chuong = Chuong::create([
                        'ten' => $c['ten'],
                        'id_hoc_phan' => $hocphan->id
                    ]);

                    // Liên kết chương với chuẩn đầu ra
                    if (isset($c['chuan_dau_ras']) && is_array($c['chuan_dau_ras'])) {
                        foreach ($c['chuan_dau_ras'] as $noi_dung) {
                            $cdr = ChuanDauRa::where('noi_dung', $noi_dung)
                                           ->where('id_hoc_phan', $hocphan->id)
                                           ->first();
                            if ($cdr) {
                                ChuongChuanDauRa::create([
                                    'id_chuong' => $chuong->id,
                                    'id_chuan_dau_ra' => $cdr->id
                                ]);
                            }
                        }
                    }
                }
            }
            // Log::info('commit');
            // DB::commit();
            return redirect()->route('admin.hocphan.index');
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     return back()->withErrors(['message' => 'Có lỗi xảy ra khi tạo học phần: ' . $e->getMessage()]);
        // }
    }

    public function edit($id)
    {
        $hocphan = HocPhan::with([
            'chuongs',
            'chuanDauRas',
            'chuongs.chuongChuanDauRa.chuanDauRa',
            'boMon',
            'boMon.khoa',
            'bacDaoTao'
        ])->find($id);
        
        $khoas = Khoa::where('able', true)
            ->whereNotIn('id', ['admin', 'DBCL'])
            ->get();
        $bomons = BoMon::where('able', true)
            ->whereHas('khoa', function($query) {
                $query->whereNotIn('id', ['admin', 'DBCL']);
            })
            ->get();
        $bacdaotaos = BacDaoTao::where('able', true)->get();
        
        return Inertia::render('Admin/HocPhan/Edit', compact('hocphan', 'bomons', 'bacdaotaos', 'khoas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ten' => 'required|string|max:255',
            'id_bo_mon' => 'required|exists:bo_mons,id',
            'id_bac_dao_tao' => 'required|exists:bac_dao_taos,id',
            'so_tin_chi' => 'required|integer|min:0',
            'chuan_dau_ras' => 'array',
            'chuan_dau_ras.*.ten' => 'required|string',
            'chuan_dau_ras.*.noi_dung' => 'required|string',
            'chuongs' => 'array',
            'chuongs.*.ten' => 'required|string',
            'chuongs.*.chuan_dau_ras' => 'array'
        ]);

        try {
            DB::beginTransaction();

            $hocphan = HocPhan::findOrFail($id);
            
            // Cập nhật thông tin học phần
            $hocphan->update($request->only([
                'ten', 'so_tin_chi', 'id_bo_mon', 'id_bac_dao_tao'
            ]));

            // Xóa tất cả các liên kết chuong_chuan_dau_ra cũ (xóa thật sự vì là bảng trung gian)
            ChuongChuanDauRa::whereIn('id_chuong', $hocphan->chuongs->pluck('id'))->delete();
            
            // Đánh dấu tất cả các chương hiện tại là không khả dụng (soft delete)
            Chuong::where('id_hoc_phan', $id)->where('able', true)->update(['able' => false]);

            // Đánh dấu tất cả các chuẩn đầu ra hiện tại là không khả dụng (soft delete)
            ChuanDauRa::where('id_hoc_phan', $id)->where('able', true)->update(['able' => false]);
            
            // Mảng để theo dõi ID của các chương và CDR đã tạo/cập nhật
            $createdChuongIds = [];
            $createdCDRIds = [];
            
            // Xử lý chuẩn đầu ra
            if ($request->has('chuan_dau_ras')) {
                foreach ($request->chuan_dau_ras as $index => $cdrData) {
                    $cdr = null;
                    
                    // Trường hợp 1: Tìm CDR có able=false và tên trùng để update nội dung
                    $cdr = ChuanDauRa::where('id_hoc_phan', $id)
                                    ->where('able', false)
                                    ->where('ten', $cdrData['ten'])
                                    ->first();
                    
                    if ($cdr) {
                        $cdr->noi_dung = $cdrData['noi_dung'];
                        $cdr->able = true;
                        $cdr->save();
                        Log::info("Cập nhật CDR có tên trùng: " . $cdr->id);
                    } else {
                        // Trường hợp 2: Tìm bất kỳ CDR able=false nào để update
                        $cdr = ChuanDauRa::where('id_hoc_phan', $id)
                                        ->where('able', false)
                                        ->first();
                        
                        if ($cdr) {
                            $cdr->ten = $cdrData['ten'];
                            $cdr->noi_dung = $cdrData['noi_dung'];
                            $cdr->able = true;
                            $cdr->save();
                            Log::info("Cập nhật CDR khác: " . $cdr->id);
                        } else {
                            // Trường hợp 3: Tạo mới CDR
                            $cdr = ChuanDauRa::create([
                                'ten' => $cdrData['ten'],
                                'noi_dung' => $cdrData['noi_dung'],
                                'id_hoc_phan' => $id,
                                'able' => true
                            ]);
                            Log::info("Tạo mới CDR: " . $cdr->id);
                        }
                    }
                    
                    // Lưu ID và nội dung để tham chiếu sau này
                    $createdCDRIds[$cdrData['noi_dung']] = $cdr->id;
                }
            }
            
            // Xử lý chương
            if ($request->has('chuongs')) {
                foreach ($request->chuongs as $index => $chuongData) {
                    $chuong = null;
                    
                    // Trường hợp 1: Tìm chương có able=false để update
                    $chuong = Chuong::where('id_hoc_phan', $id)
                                   ->where('able', false)
                                   ->first();
                    
                    if ($chuong) {
                        $chuong->ten = $chuongData['ten'];
                        $chuong->able = true;
                        $chuong->save();
                        Log::info("Cập nhật chương: " . $chuong->id);
                    } else {
                        // Trường hợp 2: Tạo mới chương
                        $chuong = Chuong::create([
                            'ten' => $chuongData['ten'],
                            'id_hoc_phan' => $id,
                            'able' => true
                        ]);
                        Log::info("Tạo mới chương: " . $chuong->id);
                    }
                    
                    $createdChuongIds[] = $chuong->id;
                    
                    // Liên kết chương với chuẩn đầu ra
                    if (isset($chuongData['chuan_dau_ras']) && is_array($chuongData['chuan_dau_ras'])) {
                        foreach ($chuongData['chuan_dau_ras'] as $noi_dung) {
                            // Tìm ID của CDR dựa trên nội dung
                            if (isset($createdCDRIds[$noi_dung])) {
                                $cdrId = $createdCDRIds[$noi_dung];
                                
                                // Tạo liên kết mới
                                ChuongChuanDauRa::create([
                                    'id_chuong' => $chuong->id,
                                    'id_chuan_dau_ra' => $cdrId,
                                    'able' => true
                                ]);
                                Log::info("Tạo liên kết chương-CDR: {$chuong->id} - {$cdrId}");
                            } else {
                                // Nếu không tìm thấy CDR dựa trên nội dung, tìm dựa trên id_hoc_phan
                                $cdr = ChuanDauRa::where('noi_dung', $noi_dung)
                                               ->where('id_hoc_phan', $id)
                                               ->where('able', true)
                                               ->first();
                                
                                if ($cdr) {
                                    ChuongChuanDauRa::create([
                                        'id_chuong' => $chuong->id,
                                        'id_chuan_dau_ra' => $cdr->id,
                                        'able' => true
                                    ]);
                                    Log::info("Tạo liên kết chương-CDR (tìm kiếm): {$chuong->id} - {$cdr->id}");
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.hocphan.index')->with('message', 'Cập nhật học phần thành công');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Lỗi cập nhật học phần: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->route('admin.hocphan.index')->withErrors(['message' => 'Có lỗi xảy ra khi cập nhật học phần: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $hocphan = HocPhan::findOrFail($id);
            
            // Cập nhật trạng thái able = false cho học phần và các bảng liên quan
            $hocphan->able = false;
            $hocphan->save();
            
            ChuanDauRa::where('id_hoc_phan', $id)->update(['able' => false]);
            
            foreach ($hocphan->chuongs as $chuong) {
                $chuong->able = false;
                $chuong->save();
                
                ChuongChuanDauRa::where('id_chuong', $chuong->id)->update(['able' => false]);
            }

            DB::commit();
            return redirect()->route('admin.hocphan.index');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['message' => 'Có lỗi xảy ra khi xóa học phần: ' . $e->getMessage()]);
        }
    }
}
