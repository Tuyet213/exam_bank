<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChuanDauRa;
use Inertia\Inertia;
class ChuanDauRaController extends Controller
{
    public function index()
    {
        $chuanDauRas = ChuanDauRa::where('able', true)->paginate(10);
        return Inertia::render('Admin/ChuanDauRa/Index', compact('chuanDauRas'));
    }

    public function create()
    {
        return Inertia::render('Admin/ChuanDauRa/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|string|max:255',
            'noi_dung' => 'required|string|max:255',
        ]);

        ChuanDauRa::create($request->all());
        return redirect()->route('admin.chuandaura.index');
        
        
    }

    public function edit($id)
    {
        $chuanDauRa = ChuanDauRa::find($id);
        return Inertia::render('Admin/ChuanDauRa/Edit', compact('chuanDauRa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ten' => 'required|string|max:255',
            'noi_dung' => 'required|string|max:255',
        ]);
        $chuanDauRa = ChuanDauRa::find($id);
        $chuanDauRa->update($request->all());
        return redirect()->route('admin.chuandaura.index');
    }

    public function destroy($id)
    {
        $chuanDauRa = ChuanDauRa::find($id);
        $chuanDauRa->able = false;
        $chuanDauRa->save();
        return redirect()->route('admin.chuandaura.index');
    }
}
