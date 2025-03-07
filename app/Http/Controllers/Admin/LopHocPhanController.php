<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LopHocPhan;
use App\Models\Khoa;
use App\Models\HocPhan;
use App\Models\User;
use Inertia\Inertia;

class LopHocPhanController extends Controller
{
    public function index()
    {
        $lophocphans = LopHocPhan::where('able', true)->with('hocPhan','vienChuc', 'khoa')->paginate(10);
        return Inertia::render('Admin/LopHocPhan/Index', compact('lophocphans'));
    }

    public function create()
    {
        $khoas = Khoa::where('able', true)->get();
        $hoc_phans = HocPhan::where('able', true)->get();
        $vien_chucs = User::where([['able', true]])->get();
        return Inertia::render('Admin/LopHocPhan/Create', compact('khoas', 'hoc_phans', 'vien_chucs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|string|max:255',
            'ky_hoc' => 'nullable|string|max:255',
            'nam_hoc' => 'nullable|string|max:255',
            'so_luong_sinh_vien' => 'nullable|integer',
            'id_khoa' => 'nullable|string|max:6',
            'id_hoc_phan' => 'nullable|string|max:6',
            'id_vien_chuc' => 'nullable|string|max:6',
        ]);

        LopHocPhan::create($request->all());
        return redirect()->route('admin.lophocphan.index')->with('success', 'Lớp học phần đã được tạo thành công.');
    }

    public function edit($id)
    {
        $lophocphan = LopHocPhan::findOrFail($id);
        $khoas = Khoa::where('able', true)->get();
        $hoc_phans = HocPhan::where('able', true)->get();
        $vien_chucs = User::where([['able', true]])->get();
        return Inertia::render('Admin/LopHocPhan/Edit', compact('lophocphan', 'khoas', 'hoc_phans', 'vien_chucs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ten' => 'required|string|max:255',
            'ky_hoc' => 'required|string|max:255',
            'nam_hoc' => 'required|string|max:255',
            'so_luong_sinh_vien' => 'required|integer',
            'id_khoa' => 'required|string|max:6',
            'id_hoc_phan' => 'required|string|max:6',
            'id_vien_chuc' => 'required|string|max:6',
        ]);

        $lophocphan = LopHocPhan::findOrFail($id);
        $lophocphan->update($request->all());
        return redirect()->route('admin.lophocphan.index')->with('success', 'Lớp học phần đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $lophocphan = LopHocPhan::findOrFail($id);
        $lophocphan->update(['able' => false]);
        return redirect()->route('admin.lophocphan.index')->with('success', 'Lớp học phần đã được vô hiệu hóa.');
    }
}
