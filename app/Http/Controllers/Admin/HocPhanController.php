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
    public function index(Request $request)
    {
        $query = HocPhan::where('able', true)->with('bomon', 'bacdaotao', 'bomon.khoa');

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

        $khoas = \App\Models\Khoa::where('able', true)->get(['id', 'ten']);
        $bomons = BoMon::where('able', true)->with('khoa')->get(['id', 'ten', 'id_khoa']);
        $bacdaotaos = BacDaoTao::where('able', true)->get(['id', 'ten']);

        return Inertia::render('Admin/HocPhan/Index', compact('hocphans', 'bomons', 'bacdaotaos', 'khoas'));
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
