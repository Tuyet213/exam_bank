<?php

namespace App\Http\Controllers\RegisterProcess;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CTDSDangKy;
use App\Models\DSDangKy;
use App\Models\HocPhan;
use App\Models\User;
use Inertia\Inertia;
use App\Imports\CTDSDangKyImport;
use Maatwebsite\Excel\Facades\Excel;
use phpDocumentor\Reflection\PseudoTypes\True_;
class CTDSDangKyController extends Controller
{
    public function index($id)
    {
        $can_create = True;
        $count = 0;
        $dsdangky = DSDangKy::with(['boMon'])->findOrFail($id);
        $chitiet = CTDSDangKy::with(['hocPhan', 'vienChuc'])
            ->where('id_ds_dang_ky', $id)
            ->where('able', true)
            ->get()
            ->map(function ($ct) use (&$count) {
                if ($ct->trang_thai == 'Draft') {
                    $count++;
                }
                return [
                    'id' => $ct->id,
                    'ten' => $ct->ten,
                    'hoc_phan' => $ct->hocPhan->ten,
                    'vien_chuc' => $ct->vienChuc->name,
                    'so_gio' => $ct->so_gio,
                    'trang_thai' => $ct->trang_thai
                ];
            });

        $hocphans = HocPhan::where('able', true)->get();
        $vienchucs = User::where('able', true)->get();
        if ($count >= 1) {
            $can_create = False;
        }

        return Inertia::render('RegisterProcess/CTDSDangKy/Index', 
            compact('dsdangky', 'chitiet', 'hocphans', 'vienchucs', 'can_create'));
    }
    public function create($id)
    {
        $dsdangky = DSDangKy::with(['boMon'])->findOrFail($id);
        $hocphans = HocPhan::where('able', true)->where('id_bo_mon', $dsdangky->id_bo_mon)->get();
        $vienchucs = User::where('able', true)->where('id_bo_mon', $dsdangky->id_bo_mon)->get();
        return Inertia::render('RegisterProcess/CTDSDangKy/Create', compact('dsdangky', 'hocphans', 'vienchucs'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'id_ds_dang_ky' => 'required|exists:d_s_dang_kies,id',
            'id_hoc_phan' => 'required|exists:hoc_phans,id',
            'id_vien_chuc' => 'required|exists:users,id',
            'so_gio' => 'required|numeric|min:1'
        ]);

        CTDSDangKy::create([
            'id_ds_dang_ky' => $request->id_ds_dang_ky,
            'id_hoc_phan' => $request->id_hoc_phan,
            'id_vien_chuc' => $request->id_vien_chuc,
            'so_gio' => $request->so_gio,
            'trang_thai' => 'Draft',
            'able' => true
        ]);

        return redirect()->route('tbm.ctdsdangky.index', $request->id_ds_dang_ky);
    }

    public function edit($id)
    {
        $ctdsdangky = CTDSDangKy::findOrFail($id);
        $hocphans = HocPhan::where('able', true)->where('id_bo_mon', $ctdsdangky->id_bo_mon)->get();
        $vienchucs = User::where('able', true)->where('id_bo_mon', $ctdsdangky->id_bo_mon)->get();
        return Inertia::render('RegisterProcess/CTDSDangKy/Edit', compact('ctdsdangky', 'hocphans', 'vienchucs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_hoc_phan' => 'required|exists:hoc_phans,id',
            'id_vien_chuc' => 'required|exists:users,id',
            'so_gio' => 'required|numeric|min:1'
        ]);

        $ctdsdangky = CTDSDangKy::findOrFail($id);
        $ctdsdangky->update([
            'id_hoc_phan' => $request->id_hoc_phan,
            'id_vien_chuc' => $request->id_vien_chuc,
            'so_gio' => $request->so_gio
        ]);

        return redirect()->route('tbm.ctdsdangky.index', $ctdsdangky->id_ds_dang_ky);
    }

    public function destroy($id)
    {
        $ctdsdangky = CTDSDangKy::findOrFail($id);
        $ctdsdangky->delete();
        return redirect()->route('tbm.ctdsdangky.index', $ctdsdangky->id_ds_dang_ky);
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $dsDangKy = DSDangKy::findOrFail($request->id_ds_dang_ky);
            
            Excel::import(
                new CTDSDangKyImport($request->id_ds_dang_ky, $dsDangKy->id_bo_mon),
                $request->file('file')
            );

            return redirect()->back()->with([
                'success' => 'Import dữ liệu thành công!',
                'error' => null
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'success' => null,
                'error' => 'Có lỗi xảy ra khi import: ' . $e->getMessage()
            ]);
        }
    }
}
