<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChucVu;
use Inertia\Inertia;

class ChucVuController extends Controller
{
    public function index(Request $request)
    {
        $query = ChucVu::where('able', true);

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

        $chucVus = $query->paginate(10)->withQueryString();

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

        ChucVu::create($request->all());
        return redirect()->route('admin.chucvu.index');
        
        
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
