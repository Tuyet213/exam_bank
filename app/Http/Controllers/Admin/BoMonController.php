<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoMon;
use App\Models\Khoa;
use Inertia\Inertia;

class BoMonController extends Controller
{
    public function index(Request $request)
    {
        $query = BoMon::where('able', true)->with('khoa');

        if ($request->has('search') && !empty($request->input('search'))) {
            $searchTerm = $request->input('search');
            $filterBy = $request->input('filter', 'all');

            if ($filterBy === 'id') {
                $query->where('id', 'like', "%{$searchTerm}%");
            } elseif ($filterBy === 'ten') {
                $query->where('ten', 'like', "%{$searchTerm}%");
            } elseif ($filterBy === 'khoa') {
                $query->whereHas('khoa', function ($q) use ($searchTerm) {
                    $q->where('ten', 'like', "%{$searchTerm}%");
                });
            } else {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('id', 'like', "%{$searchTerm}%")
                      ->orWhere('ten', 'like', "%{$searchTerm}%")
                      ->orWhereHas('khoa', function ($q) use ($searchTerm) {
                          $q->where('ten', 'like', "%{$searchTerm}%");
                      });
                });
            }
        }

        $bomons = $query->paginate(10)->withQueryString();

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

        try {
            $bomon = BoMon::create($request->all());
            return redirect()->route('admin.bomon.index')->with('success', 'Bộ môn đã được thêm thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.bomon.index')->with('error', 'Có lỗi xảy ra khi thêm Bộ môn!');
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
        return redirect()->route('admin.bomon.index')->with('success', 'Bộ môn đã được cập nhật thành công!');
    }

    public function destroy($id)
    {
        $bomon = BoMon::find($id);
        $bomon->able = false;
        $bomon->save();
        return redirect()->route('admin.bomon.index')->with('success', 'Bộ môn đã được xóa thành công!');
    }
}