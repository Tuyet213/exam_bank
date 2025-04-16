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

class DSDangKyController extends Controller
{
    public function index(Request $request)
    {
        $query = DSDangKy::with(['boMon', 'ctDSDangKies'])
            ->where('able', true)
            ->where('id_bo_mon', Auth::user()->id_bo_mon)
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $query->where('ten', 'like', '%' . $request->input('search') . '%');
        }

        $danhsachs = $query->paginate(10)->through(function ($ds) {
            $canSend = !$ds->ctDSDangKies->count() || 
                       $ds->ctDSDangKies->where('trang_thai', 'Draft')->count() > 0;
            return [
                'id' => $ds->id,
                'ten' => $ds->ten,
                'bo_mon' => $ds->boMon->ten,
                'hoc_ki' => 'Học kỳ ' . $ds->hoc_ki,
                'nam_hoc' => $ds->nam_hoc,
                'can_send' => $canSend
            ];
        });

        $bo_mon = Auth::user()->boMon->ten;

        return Inertia::render('TBM/DSDangKy/Index', compact('danhsachs', 'bo_mon'));
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
                $query->where('name', 'quality');
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

        // try {
        //     DB::beginTransaction();

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
                foreach ($ct['id_vien_chuc'] as $idVienChuc) {
                    CTDSDangKy::create([
                        'id_ds_dang_ky' => $dsDangKy->id,
                        'id_hoc_phan' => $ct['id_hoc_phan'],
                        'id_vien_chuc' => $idVienChuc,
                        'loai_ngan_hang' => $ct['loai_ngan_hang'],
                        'so_luong' => $ct['so_luong'],
                        'trang_thai' => 'Draft',
                        'so_gio' => 0
                    ]);
                }
            }

        //     DB::commit();

        //     return redirect()->route('tbm.dsdangky.index')
        //         ->with('success', true)
        //         ->with('message', 'Tạo danh sách đăng ký thành công!');

        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return redirect()->back()
        //         ->with('success', false)
        //         ->with('message', 'Có lỗi xảy ra: ' . $e->getMessage());
        // }
        return redirect()->route('tbm.dsdangky.index')->with('message', 'Tạo danh sách đăng ký thành công!');
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
