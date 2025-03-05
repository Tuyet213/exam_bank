<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChucVu;
use Inertia\Inertia;

class ChucVuController extends Controller
{
    public function index()
    {
        $chucVus = ChucVu::where('able', true)->paginate(10);
        return Inertia::render('Admin/ChucVu/Index', compact('chucVus'));
    }

    public function create()
    {
        return Inertia::render('Admin/ChucVu/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:6',
            'ten' => 'required|string|max:255',
        ]);

        try{
            $chucVu = ChucVu::create($request->all());
            return redirect()->route('admin.chucvu.index');
        }catch(\Exception $e){
            return redirect()->route('admin.chucvu.index');
        }
        
    }

    public function edit($id)
    {
        $chucVu = ChucVu::find($id);
        return Inertia::render('Admin/ChucVu/Edit', compact('chucVu'));
    }

    public function update(Request $request, $id)
    {
        $chucVu = ChucVu::find($id);
        $chucVu->update($request->all());
        return redirect()->route('admin.chucvu.index');
    }

    public function destroy($id)
    {
        $chucVu = ChucVu::find($id);
        $chucVu->able = false;
        $chucVu->save();
        return redirect()->route('admin.chucvu.index');
    }
}
