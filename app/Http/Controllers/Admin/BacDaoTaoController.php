<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BacDaoTao;
use Inertia\Inertia;
class BacDaoTaoController extends Controller
{
    public function index()
    {
        $bacdaotaos = BacDaoTao::where('able', true)->paginate(10);
        return Inertia::render('Admin/BacDaoTao/Index', compact('bacdaotaos'));
    }

    public function create()
    {
        return Inertia::render('Admin/BacDaoTao/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:6',
            'ten' => 'required|string|max:255',
        ]);

        BacDaoTao::create($request->all());
        return redirect()->route('admin.bacdaotao.index');
        
    }

    public function edit($id)
    {
        $bacdaotao = BacDaoTao::find($id);
        return Inertia::render('Admin/BacDaoTao/Edit', compact('bacdaotao'));
    }

    public function update(Request $request, $id)
    {
        $bacdaotao = BacDaoTao::find($id);
        $bacdaotao->update($request->all());
        return redirect()->route('admin.bacdaotao.index');
    }

    public function destroy($id)
    {
        $bacdaotao = BacDaoTao::find($id);
        $bacdaotao->able = false;
        $bacdaotao->save();
        return redirect()->route('admin.bacdaotao.index');
    }
}
