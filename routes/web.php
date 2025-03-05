<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\KhoaController;
use App\Http\Controllers\Admin\BoMonController;
use App\Http\Controllers\Admin\ChucVuController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// chỉ trả về component hoặc page vì nó tự import vào app.blade.php

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::prefix('admin')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');

    // Khoa
    Route::get('/khoa', [KhoaController::class, 'index'])->name('admin.khoa.index');
    Route::get('/khoa/create', [KhoaController::class, 'create'])->name('admin.khoa.create');
    Route::post('/khoa/store', [KhoaController::class, 'store'])->name('admin.khoa.store');
    Route::get('/khoa/edit/{id}', [KhoaController::class, 'edit'])->name('admin.khoa.edit');
    Route::put('/khoa/update/{id}', [KhoaController::class, 'update'])->name('admin.khoa.update');
    Route::delete('/khoa/destroy/{id}', [KhoaController::class, 'destroy'])->name('admin.khoa.destroy');

    // Bo Mon
    Route::get('/bomon', [BoMonController::class, 'index'])->name('admin.bomon.index');
    Route::get('/bomon/create', [BoMonController::class, 'create'])->name('admin.bomon.create');
    Route::post('/bomon/store', [BoMonController::class, 'store'])->name('admin.bomon.store');
    Route::get('/bomon/edit/{id}', [BoMonController::class, 'edit'])->name('admin.bomon.edit');
    Route::put('/bomon/update/{id}', [BoMonController::class, 'update'])->name('admin.bomon.update');
    Route::delete('/bomon/destroy/{id}', [BoMonController::class, 'destroy'])->name('admin.bomon.destroy');

    // Chuc Vu
    Route::get('/chucvu', [ChucVuController::class, 'index'])->name('admin.chucvu.index');
    Route::get('/chucvu/create', [ChucVuController::class, 'create'])->name('admin.chucvu.create');
    Route::post('/chucvu/store', [ChucVuController::class, 'store'])->name('admin.chucvu.store');
    Route::get('/chucvu/edit/{id}', [ChucVuController::class, 'edit'])->name('admin.chucvu.edit');
    Route::put('/chucvu/update/{id}', [ChucVuController::class, 'update'])->name('admin.chucvu.update');
    Route::delete('/chucvu/destroy/{id}', [ChucVuController::class, 'destroy'])->name('admin.chucvu.destroy');
});




Route::middleware('auth')->group(function () {
    Route::get('/admin/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/admin/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('/admin/profile', [ProfileController::class, 'destroy'])->name('admin.profile.destroy');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
