<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GioQuyDoi;   
use Inertia\Inertia;
class GioQuyDoiController extends Controller
{
    public function index(Request $request)
    {
        $query = GioQuyDoi::where('able', true);

        if ($request->has('loai_de_thi') && $request->input('loai_de_thi') !== '') {
            $query->where('loai_de_thi', $request->input('loai_de_thi'));
        }

        if ($request->has('loai_hanh_dong') && $request->input('loai_hanh_dong') !== '') {
            $query->where('loai_hanh_dong', $request->input('loai_hanh_dong'));
        }

        if ($request->has('search') && !empty($request->input('search'))) {
            $query->where('id', 'like', "%{$request->input('search')}%");
        }

        $gioQuyDois = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/GioQuyDoi/Index', compact('gioQuyDois'));
    }

    public function create()
    {
        return Inertia::render('Admin/GioQuyDoi/Create');
    }

    public function store(Request $request)
    {
        $gioQuyDoi = GioQuyDoi::create($request->all());
        return redirect()->route('admin.gioquydoi.index');
    }

    public function edit($id)
    {
        $gioQuyDoi = GioQuyDoi::find($id);
        return Inertia::render('Admin/GioQuyDoi/Edit', compact('gioQuyDoi'));
    }

    public function update(Request $request, $id)
    {
        $gioQuyDoi = GioQuyDoi::find($id);
        $gioQuyDoi->update($request->all());
        return redirect()->route('admin.gioquydoi.index');
    }   

    public function destroy($id)
    {
        $gioQuyDoi = GioQuyDoi::find($id);
        $gioQuyDoi->able = 0;
        $gioQuyDoi->save();
        return redirect()->route('admin.gioquydoi.index');
    }

}
