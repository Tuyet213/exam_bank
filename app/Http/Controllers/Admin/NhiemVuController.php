<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NhiemVu;
use Inertia\Inertia;
class NhiemVuController extends Controller
{
    public function index(Request $request)
    {
        $query = NhiemVu::where('able', true);

        if ($request->has('search') && !empty($request->input('search'))) {
            $searchTerm = $request->input('search');
            $filterBy = $request->input('filter', 'all');

            if ($filterBy === 'id') {
                $query->where('id', 'like', "%{$searchTerm}%");
            } elseif ($filterBy === 'ten') {
                $query->where('ten', 'like', "%{$searchTerm}%");
            } else {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('id', 'like', "%{$searchTerm}%")
                      ->orWhere('ten', 'like', "%{$searchTerm}%");
                });
            }
        }

        $nhiemvus = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/NhiemVu/Index', compact('nhiemvus'));
    }

    public function create()
    {
        return Inertia::render('Admin/NhiemVu/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|string|max:255',
        ]);

        try{
            $nhiemvu = NhiemVu::create($request->all());
            return redirect()->route('admin.nhiemvu.index');
        }catch(\Exception $e){
            return redirect()->route('admin.nhiemvu.index');
        }
        
    }

    public function edit($id)
    {
        $nhiemvu = NhiemVu::find($id);
        return Inertia::render('Admin/NhiemVu/Edit', compact('nhiemvu'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'ten' => 'required|string|max:255',
        ]);
        $nhiemvu = NhiemVu::findOrFail($id);
        $nhiemvu->update($validated);
        return redirect()->route('admin.nhiemvu.index')->with('success', 'Nhiệm vụ đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $nhiemvu = NhiemVu::findOrFail($id);
        $nhiemvu->able = false;
        $nhiemvu->save();
        return redirect()->route('admin.nhiemvu.index');
    }
}
