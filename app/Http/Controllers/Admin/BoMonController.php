<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoMon;
use App\Models\Khoa;
use Inertia\Inertia;

class BoMonController extends Controller
{
    public function index()
    {
        $bomons = BoMon::where('able', true)->with('khoa')->paginate(10);
        return Inertia::render('Admin/BoMon/Index', compact('bomons'));
    }

    public function create()
    {
        $khoas = Khoa::where('able', true)->get();
        return Inertia::render('Admin/BoMon/Create', compact('khoas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:6',
            'ten' => 'required|string|max:255',
            'id_khoa' => 'required|exists:khoas,id',
        ]);

        try{
            $bomon = BoMon::create($request->all());
            return redirect()->route('admin.bomon.index');
        }catch(\Exception $e){
            return redirect()->route('admin.bomon.index');
        }
        
    }

    public function edit($id)
    {
        $bomon = BoMon::find($id);
        $khoas = Khoa::where('able', true)->get();
        return Inertia::render('Admin/BoMon/Edit', compact('bomon', 'khoas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id' => 'required|string|max:6',
            'ten' => 'required|string|max:255',
            'id_khoa' => 'required|exists:khoas,id',
        ]);
        $bomon = BoMon::find($id);
        $bomon->update($request->all());
        return redirect()->route('admin.bomon.index');
    }

    public function destroy($id)
    {
        $bomon = BoMon::find($id);
        $bomon->able = false;
        $bomon->save();
        return redirect()->route('admin.bomon.index');
    }
}


