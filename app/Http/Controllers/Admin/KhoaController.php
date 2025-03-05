<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Khoa;
use Inertia\Inertia;
class KhoaController extends Controller
{
    public function index()
    {
        $khoas = Khoa::where('able', true)->paginate(10);
        return Inertia::render('Admin/Khoa/Index', compact('khoas'));
    }

    public function create()
    {
        return Inertia::render('Admin/Khoa/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:6',
            'ten' => 'required|string|max:255',
        ]);

        try{
            $khoa = Khoa::create($request->all());
            return redirect()->route('admin.khoa.index');
        }catch(\Exception $e){
            return redirect()->route('admin.khoa.index');
        }
        
    }

    public function edit($id)
    {
        $khoa = Khoa::find($id);
        return Inertia::render('Admin/Khoa/Edit', compact('khoa'));
    }

    public function update(Request $request, $id)
    {
        $khoa = Khoa::find($id);
        $khoa->update($request->all());
        return redirect()->route('admin.khoa.index');
    }

    public function destroy($id)
    {
        $khoa = Khoa::find($id);
        $khoa->able = false;
        $khoa->save();
        return redirect()->route('admin.khoa.index');
    }
}
