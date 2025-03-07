<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HocPhan;
use App\Models\BoMon;
use App\Models\BacDaoTao;
use Inertia\Inertia;
class HocPhanController extends Controller
{
    public function index()
    {
        $hocphans = HocPhan::where('able', true)->with('bomon','bacdaotao')->paginate(10);
        return Inertia::render('Admin/HocPhan/Index', compact('hocphans'));
    }

    public function create()
    {
        $bomons = BoMon::where('able', true)->get();
        $bacdaotaos = BacDaoTao::where('able', true)->get();
        return Inertia::render('Admin/HocPhan/Create', compact('bomons', 'bacdaotaos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:6',
            'ten' => 'required|string|max:255',
            'id_bo_mon' => 'required|exists:bo_mons,id',
            'id_bac_dao_tao' => 'required|exists:bac_dao_taos,id',
            'so_tin_chi' => 'required|integer|min:0',
            'hoc_phi' => 'required|numeric|min:0',
        ]);

        HocPhan::create($request->all());
        return redirect()->route('admin.hocphan.index');
        
    }

    public function edit($id)
    {
        $hocphan = HocPhan:: find($id);
        $bomons = BoMon::where('able', true)->get();
        $bacdaotaos = BacDaoTao::where('able', true)->get();
        return Inertia::render('Admin/HocPhan/Edit', compact('hocphan', 'bomons', 'bacdaotaos'));
    }

    public function update(Request $request, $id)
    {
        $hocphan = HocPhan::find($id);
        $hocphan->update($request->all());
        return redirect()->route('admin.hocphan.index');
    }

    public function destroy($id)
    {
        $hocphan = HocPhan::find($id);
        $hocphan->able = false;
        $hocphan->save();
        return redirect()->route('admin.hocphan.index');
    }
}
