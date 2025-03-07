<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChucVu;
use App\Models\BoMon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function index()
    {
        $users = User::where('able', true)->with('chucvu', 'bomon')->paginate(10);
        $users->map(function ($user) {
            $user->ngay_sinh = date('d/m/Y', strtotime($user->ngay_sinh));
            return $user;
        });
        return Inertia::render('Admin/User/Index', compact('users'));
    }

    public function create()
    {
        $chucvus = ChucVu::where('able', true)->get();
        $bomons = BoMon::where('able', true)->get();
        return Inertia::render('Admin/User/Create', compact('chucvus', 'bomons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:6',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'sdt' => 'required|string|regex:/^[0-9]+$/|min:10|max:11',
            'dia_chi' => 'required|string|max:255',
            'ngay_sinh' => 'required|date',
            'gioi_tinh' => 'required|string|max:255',
            'id_chucvu' => 'required|exists:chucvus,id',
            'id_bomon' => 'required|exists:bomons,id',
        ]);

        try{
            $user = User::create([
                'id' => $request->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'sdt' => $request->sdt,
                'dia_chi' => $request->tinh_name . '-' . $request->quan_name . '-' . $request->xa_name . '-' . $request->more_address,
                'ngay_sinh' => $request->ngay_sinh,
                'gioi_tinh' => $request->gioi_tinh,
                'id_chucvu' => $request->id_chucvu,
                'id_bomon' => $request->id_bomon,
            ]);
            return redirect()->route('admin.user.index');
        }catch(\Exception $e){
            return redirect()->route('admin.user.index');
        }
        
    }

    public function edit($id)
    {
        $user = User::find($id);
        $chucvus = ChucVu::where('able', true)->get();
        $bomons = BoMon::where('able', true)->get();
        return Inertia::render('Admin/User/Edit', compact('user', 'chucvus', 'bomons'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->update($request->all());
        return redirect()->route('admin.user.index');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->able = false;
        $user->save();
        return redirect()->route('admin.user.index');
    }
}
