<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChucVu;
use App\Models\BoMon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;
class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('able', true)->with('chucvu', 'bomon.khoa');
        if($request->has('search') && !empty($request->input('search'))) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                  ->orWhere('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('sdt', 'like', "%{$searchTerm}%");
            });
        }

        if($request->has('id_bo_mon') && !empty($request->input('id_bo_mon'))) {
            $query->where('id_bo_mon', $request->input('id_bo_mon'));
        }

        if($request->has('id_chuc_vu') && !empty($request->input('id_chuc_vu'))) {
            $query->where('id_chuc_vu', $request->input('id_chuc_vu'));
        }
        $users = $query->paginate(10);
        $users->map(function ($user) {
            $user->ngay_sinh = date('d/m/Y', strtotime($user->ngay_sinh));
            return $user;
        });
        $bomons = BoMon::where('able', true)->get(['id', 'ten']);
        $chucvus = ChucVu::where('able', true)->get(['id', 'ten']);
        return Inertia::render('Admin/User/Index', compact('users', 'bomons', 'chucvus'));
    }

    public function create()
    {
        $chucvus = ChucVu::where('able', true)->get();
        $bomons = BoMon::where('able', true)->get();
        $roles = Role::all();
        $permissions = Permission::all();
        return Inertia::render('Admin/User/Create', compact('chucvus', 'bomons', 'roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:6',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'sdt' => 'required|string|regex:/^[0-9]+$/|min:10|max:11',
            'ngay_sinh' => 'required|date',
            'gioi_tinh' => 'required|boolean',
            'id_chuc_vu' => 'required|exists:chuc_vus,id',
            'id_bo_mon' => 'required|exists:bo_mons,id',
            'roles' => 'array',
            'permissions' => 'array',
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
                'id_chuc_vu' => $request->id_chuc_vu,
                'id_bo_mon' => $request->id_bo_mon,
            ]);

            if($request->roles) {
                $user->assignRole($request->roles);
            }

            if($request->permissions) {
                $user->givePermissionTo($request->permissions);
            }

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
        $roles = Role::all();
        $permissions = Permission::all();
        $dia_chi = explode('-', $user->dia_chi);
        $user->tinh_name = $dia_chi[0];
        $user->quan_name = $dia_chi[1];
        $user->xa_name = $dia_chi[2];
        $user->more_address = $dia_chi[3];
        $user->role_names = $user->getRoleNames()->toArray();
        $user->permission_names = $user->getPermissionNames()->toArray();
        Log::info('User roles:', [
            'roles' => $user->getRoleNames(), // Lấy danh sách tên roles
            'permissions' => $user->getPermissionNames() // Lấy danh sách tên permissions
        ]);
        return Inertia::render('Admin/User/Edit', compact('user', 'chucvus', 'bomons', 'roles', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id' => 'required|string|max:6',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id.',id',
            'sdt' => 'required|string|regex:/^[0-9]+$/|min:10|max:11',
            'ngay_sinh' => 'required|date',
            'gioi_tinh' => 'required|boolean',
            'id_chuc_vu' => 'required|exists:chuc_vus,id',
            'id_bo_mon' => 'required|exists:bo_mons,id',
            'roles' => 'array',
            'permissions' => 'array',
        ]);
        
        $user = User::find($id);
        $user->update([
            'id' => $request->id,
            'name' => $request->name,
            'email' => $request->email,
            'sdt' => $request->sdt,
            'dia_chi' => $request->tinh_name . '-' . $request->quan_name . '-' . $request->xa_name . '-' . $request->more_address,
            'ngay_sinh' => $request->ngay_sinh,
            'gioi_tinh' => $request->gioi_tinh,
            'id_chuc_vu' => $request->id_chuc_vu,
            'id_bo_mon' => $request->id_bo_mon, 
        ]);

         // Sync roles - sẽ xóa tất cả roles cũ và thêm roles mới
         if($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        // Sync permissions - sẽ xóa tất cả permissions cũ và thêm permissions mới
        if($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        // if($request->roles) {
        //     $user->assignRole($request->roles);
        // }

        // if($request->permissions) {
        //     $user->givePermissionTo($request->permissions);
        // }

        return redirect()->route('admin.user.index');
    }

    public function show($id)
{
    $user = User::with(['chucvu', 'bomon.khoa'])->findOrFail($id);
    $user->role_names = $user->getRoleNames()->toArray();
    $user->permission_names = $user->getPermissionNames()->toArray();
    $user->ngay_sinh = date('d/m/Y', strtotime($user->ngay_sinh));
    if(str_ends_with($user->dia_chi, '-')){
        $user->dia_chi = substr($user->dia_chi, 0, -1);
    }
    return Inertia::render('Admin/User/Show', compact('user'));
}
    public function destroy($id)
    {
        $user = User::find($id);
        $user->able = false;
        $user->save();
        return redirect()->route('admin.user.index');
    }
}
