<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        $role = Auth::user()->getRoleNames();
        if($role->contains('Admin')){
            return redirect(route('admin.khoa.index'));
        }
        if($role->contains('Nhân viên P.ĐBCL')){
            return redirect(route('quality.dsdangky.index'));
        }
        if($role->contains('Trưởng Bộ Môn')){
            return redirect(route('tbm.dsdangky.index'));
        }
        if($role->contains('Trưởng Khoa')){
            return redirect(route('tk.dsbienban.index'));
        }
        if($role->contains('Giảng viên')){
            return redirect(route('cauhoi.hocphan'));
        }
       // return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
