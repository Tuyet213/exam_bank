<?php

namespace App\Http\Controllers\RegisterProcess;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DSDangKy;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\DSDangKyMail;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
class DSDangKyController extends Controller
{
    public function index(Request $request)
    {
        $query = DSDangKy::with(['boMon', 'ctDSDangKies'])
            ->where('able', true)
            ->where('id_bo_mon', Auth::user()->id_bo_mon)
            ->orderBy('thoi_gian', 'desc');

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
                'thoi_gian' => date('d/m/Y', strtotime($ds->thoi_gian)),
                'can_send' => $canSend
            ];
        });

        return Inertia::render('RegisterProcess/DSDangKy/Index', compact('danhsachs'));
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
                $query->where('name', 'TBM');
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
        return Inertia::render('RegisterProcess/DSDangKy/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|string',
        ]);

        DSDangKy::create([
            'ten' => $request->ten,
            'id_bo_mon' => Auth::user()->id_bo_mon,
            'thoi_gian' => now(),
            'able' => true
        ]);

        return redirect()->route('tbm.dsdangky.index')
            ->with('message', 'Tạo danh sách đăng ký thành công!');
    }

    public function edit($id)
    {
        $dsdangky = DSDangKy::with(['boMon', 'ctDSDangKies.vienChuc', 'ctDSDangKies.hocPhan'])
            ->findOrFail($id);
        return Inertia::render('RegisterProcess/DSDangKy/Edit', compact('dsdangky'));
    }

    public function update(Request $request, $id)
    {
        $dsdangky = DSDangKy::findOrFail($id);
        $dsdangky->update($request->all());
        return redirect()->route('tbm.dsdangky.index')->with('message', 'Cập nhật danh sách đăng ký thành công!');
    }
   
    
}
