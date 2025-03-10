<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GioQuyDoi;   
use Inertia\Inertia;
class GioQuyDoiController extends Controller
{
    public function index()
    {
        $gioQuyDois = GioQuyDoi::where('able', 1)->paginate(10);
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
