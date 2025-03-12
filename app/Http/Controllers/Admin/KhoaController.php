<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Khoa;
use Inertia\Inertia;

class KhoaController extends Controller
{
    public function index(Request $request)
    {
        $query = Khoa::where('able', true);

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

        $khoas = $query->paginate(10)->withQueryString();

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

        Khoa::create($request->all());
        return redirect()->route('admin.khoa.index')->with('success', 'Khoa đã được thêm thành công!');
    }

    public function edit($id)
    {
        $khoa = Khoa::find($id);
        return Inertia::render('Admin/Khoa/Edit', compact('khoa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id' => 'required|string|max:6',
            'ten' => 'required|string|max:255',
        ]);
        $khoa = Khoa::find($id);
        $khoa->update($request->all());
        return redirect()->route('admin.khoa.index')->with('success', 'Khoa đã được cập nhật thành công!');
    }

    public function destroy($id)
    {
        $khoa = Khoa::find($id);
        $khoa->able = false;
        $khoa->save();
        return redirect()->route('admin.khoa.index')->with('success', 'Khoa đã được xóa thành công!');
    }
}