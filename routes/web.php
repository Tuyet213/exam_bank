    <?php
    
    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\Admin\KhoaController;
    use App\Http\Controllers\Admin\BoMonController;
    use App\Http\Controllers\Admin\ChucVuController;
    use App\Http\Controllers\Admin\UserController;
    use App\Http\Controllers\Admin\BacDaoTaoController;
    use App\Http\Controllers\Admin\HocPhanController;
    use App\Http\Controllers\Admin\ChuanDauRaController;
    use App\Http\Controllers\Admin\LopHocPhanController;
    use App\Http\Controllers\Admin\NhiemVuController;
    use App\Http\Controllers\Admin\GioQuyDoiController;
    use App\Http\Controllers\Admin\ThongKeController as AdminThongKeController;
    use App\Http\Controllers\QualityOffice\QualityOfficerController;
    use App\Http\Controllers\QualityOffice\ThongKeController as QualityThongKeController;
    use App\Http\Controllers\TBM\DSBienBanHopBMController;
    
    use App\Http\Controllers\TBM\DSDangKyController;
    use App\Http\Controllers\TBM\CTDSDangKyController;
    use Illuminate\Foundation\Application;
    use Illuminate\Support\Facades\Route;
    use Inertia\Inertia;
    use Illuminate\Support\Facades\Http;
    use App\Http\Controllers\TK\DSBienBanHopKhoaController;
    use App\Http\Controllers\TK\DSDangKyKhoaController;
    use App\Http\Controllers\QualityOffice\ThongKeGiangVienController as QualityThongKeGiangVienController;
    use App\Http\Controllers\QualityOffice\ThongKeHocPhanController;
    use App\Http\Controllers\Giangvien\ImportCauHoiController;
    use App\Http\Controllers\CauHoiController;

    // chỉ trả về component hoặc page vì nó tự import vào app.blade.php

    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::prefix('admin')->middleware(['auth', 'role:Admin'])->group(  function () {
        // Route::get('/dashboard', function () {
        //     return Inertia::render('Admin/Dashboard');
        // })->name('admin.dashboard');

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

        // User
        Route::get('/user', [UserController::class, 'index'])->name('admin.user.index');
        Route::get('/user/create', [UserController::class, 'create'])->name('admin.user.create');
        Route::post('/user/store', [UserController::class, 'store'])->name('admin.user.store');
        Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('admin.user.edit');
        Route::put('/user/update/{id}', [UserController::class, 'update'])->name('admin.user.update');
        Route::delete('/user/destroy/{id}', [UserController::class, 'destroy'])->name('admin.user.destroy');
        Route::get('/user/show/{id}', [UserController::class, 'show'])->name('admin.user.show');

        // Bậc đào tạo
        Route::get('/bacdaotao', [BacDaoTaoController::class, 'index'])->name('admin.bacdaotao.index');
        Route::get('/bacdaotao/create', [BacDaoTaoController::class, 'create'])->name('admin.bacdaotao.create');
        Route::post('/bacdaotao/store', [BacDaoTaoController::class, 'store'])->name('admin.bacdaotao.store');
        Route::get('/bacdaotao/edit/{id}', [BacDaoTaoController::class, 'edit'])->name('admin.bacdaotao.edit');
        Route::put('/bacdaotao/update/{id}', [BacDaoTaoController::class, 'update'])->name('admin.bacdaotao.update');
        Route::delete('/bacdaotao/destroy/{id}', [BacDaoTaoController::class, 'destroy'])->name('admin.bacdaotao.destroy');

        // Học phần
        Route::get('/hocphan', [HocPhanController::class, 'index'])->name('admin.hocphan.index');
        Route::get('/hocphan/create', [HocPhanController::class, 'create'])->name('admin.hocphan.create');
        Route::post('/hocphan/store', [HocPhanController::class, 'store'])->name('admin.hocphan.store');
        Route::get('/hocphan/edit/{id}', [HocPhanController::class, 'edit'])->name('admin.hocphan.edit');
        Route::put('/hocphan/update/{id}', [HocPhanController::class, 'update'])->name('admin.hocphan.update');
        Route::delete('/hocphan/destroy/{id}', [HocPhanController::class, 'destroy'])->name('admin.hocphan.destroy');

        // Chuẩn đầu ra
        Route::get('/chuan', [  ChuanDauRaController::class, 'index'])->name('admin.chuandaura.index');
        Route::get('/chuan/create', [ChuanDauRaController::class, 'create'])->name('admin.chuandaura.create');
        Route::post('/chuan/store', [ChuanDauRaController::class, 'store'])->name('admin.chuandaura.store');
        Route::get('/chuan/edit/{id}', [ChuanDauRaController::class, 'edit'])->name('admin.chuandaura.edit');
        Route::put('/chuan/update/{id}', [ChuanDauRaController::class, 'update'])->name('admin.chuandaura.update');
        Route::delete('/chuan/destroy/{id}', [ChuanDauRaController::class, 'destroy'])->name('admin.chuandaura.destroy');

        // Lớp học phần
        Route::get('/lophocphan', [LopHocPhanController::class, 'index'])->name('admin.lophocphan.index');
        Route::get('/lophocphan/create', [LopHocPhanController::class, 'create'])->name('admin.lophocphan.create');
        Route::post('/lophocphan/store', [LopHocPhanController::class, 'store'])->name('admin.lophocphan.store');
        Route::get('/lophocphan/edit/{id}', [LopHocPhanController::class, 'edit'])->name('admin.lophocphan.edit');
        Route::put('/lophocphan/update/{id}', [LopHocPhanController::class, 'update'])->name('admin.lophocphan.update');
        Route::delete('/lophocphan/destroy/{id}', [LopHocPhanController::class, 'destroy'])->name('admin.lophocphan.destroy');

        // Nhiệm vụ
        Route::get('/nhiemvu', [NhiemVuController::class, 'index'])->name('admin.nhiemvu.index');
        Route::get('/nhiemvu/create', [NhiemVuController::class, 'create'])->name('admin.nhiemvu.create');
        Route::post('/nhiemvu/store', [NhiemVuController::class, 'store'])->name('admin.nhiemvu.store');
        Route::get('/nhiemvu/edit/{id}', [NhiemVuController::class, 'edit'])->name('admin.nhiemvu.edit');
        Route::put('/nhiemvu/update/{id}', [NhiemVuController::class, 'update'])->name('admin.nhiemvu.update');
        Route::delete('/nhiemvu/destroy/{id}', [NhiemVuController::class, 'destroy'])->name('admin.nhiemvu.destroy');

        // Gio quy doi
        Route::get('/gioquydoi', [GioQuyDoiController::class, 'index'])->name('admin.gioquydoi.index');
        Route::get('/gioquydoi/create', [GioQuyDoiController::class, 'create'])->name('admin.gioquydoi.create');
        Route::post('/gioquydoi/store', [GioQuyDoiController::class, 'store'])->name('admin.gioquydoi.store');
        Route::get('/gioquydoi/edit/{id}', [GioQuyDoiController::class, 'edit'])->name('admin.gioquydoi.edit');
        Route::put('/gioquydoi/update/{id}', [GioQuyDoiController::class, 'update'])->name('admin.gioquydoi.update');
        Route::delete('/gioquydoi/destroy/{id}', [GioQuyDoiController::class, 'destroy'])->name('admin.gioquydoi.destroy');
        
        // Thống kê
        Route::get('/thongke', [AdminThongKeController::class, 'index'])->name('admin.thongke.index');
        Route::get('/thongke/excel', [AdminThongKeController::class, 'exportExcel'])->name('admin.thongke.excel');
        // Thống kê giảng viên
        Route::get('/thongke-giang-vien', [QualityThongKeGiangVienController::class, 'index'])->name('admin.thongke_giang_vien.index');
        Route::get('/thongke-giang-vien/excel', [QualityThongKeGiangVienController::class, 'exportExcel'])->name('quality.thongke_giang_vien.excel');
         // Thống kê học phần
         Route::get('/thongkehocphan', [ThongKeHocPhanController::class, 'index'])->name('admin.thongkehocphan.index');
         Route::get('/thongkehocphan/excel', [ThongKeHocPhanController::class, 'exportExcel'])->name('quality.thongkehocphan.excel');
    });


    Route::get('/proxy/tinh', function () {
        $response = Http::get('https://esgoo.net/api-tinhthanh/1/0.htm');
        return $response->json();
    });

    Route::get('/proxy/quan/{tinh_id}', function ($tinh_id) {
        $response = Http::get("https://esgoo.net/api-tinhthanh/2/{$tinh_id}.htm");
        return $response->json();
    });

    Route::get('/proxy/xa/{quan_id}', function ($quan_id) {
        $response = Http::get("https://esgoo.net/api-tinhthanh/3/{$quan_id}.htm");
        return $response->json();
    });

    Route::get('/proxy-imgur', function ($request) {
        $url = $request->query('url');
    
        // Đảm bảo chỉ lấy ảnh từ Imgur (tránh lỗ hổng bảo mật)
        if (!str_starts_with($url, 'https://i.imgur.com/')) {
            abort(403, 'Forbidden');
        }
    
        $image = file_get_contents($url);
        return response($image)->header('Content-Type', 'image/jpeg');
    });

    

    Route::prefix('tbm')->middleware(['auth', 'role:Trưởng Bộ Môn'])->group(function () {
        //DSDangKy
        Route::get('/dsdangky/create', [DSDangKyController::class, 'create'])->name('tbm.dsdangky.create');
        Route::post('/dsdangky/store', [DSDangKyController::class, 'store'])->name('tbm.dsdangky.store');
        Route::get('/dsdangky/index', [DSDangKyController::class, 'index'])->name('tbm.dsdangky.index');
        Route::post('/dsdangky/send/{id}', [DSDangKyController::class, 'send'])->name('tbm.dsdangky.send');
        Route::get('/dsdangky/edit/{id}', [DSDangKyController::class, 'edit'])->name('tbm.dsdangky.edit');
        Route::put('/dsdangky/update/{id}', [DSDangKyController::class, 'update'])->name('tbm.dsdangky.update');
        

        //CTDSDangKy
        Route::get('/ctdsdangky/index/{id}', [CTDSDangKyController::class, 'index'])->name('tbm.ctdsdangky.index');
        Route::get('/ctdsdangky/create/{id}', [CTDSDangKyController::class, 'create'])->name('tbm.ctdsdangky.create');
        Route::post('/ctdsdangky/store', [CTDSDangKyController::class, 'store'])->name('tbm.ctdsdangky.store');
        Route::get('/ctdsdangky/edit/{id}', [CTDSDangKyController::class, 'edit'])->name('tbm.ctdsdangky.edit');
        Route::put('/ctdsdangky/update/{id}', [CTDSDangKyController::class, 'update'])->name('tbm.ctdsdangky.update');
        Route::delete('/ctdsdangky/destroy/{id}', [CTDSDangKyController::class, 'destroy'])->name('tbm.ctdsdangky.destroy');
        Route::post('/ctdsdangky/{id_ds_dang_ky}/import', [CTDSDangKyController::class, 'import'])->name('tbm.ctdsdangky.import');

        //DSBienBanHopBM
        Route::get('/dsbienban/index', [DSBienBanHopBMController::class, 'index'])->name('tbm.dsbienban.index');
        Route::get('/dsbienban/create', [DSBienBanHopBMController::class, 'create'])->name('tbm.dsbienban.create');
        Route::post('/dsbienban/store', [DSBienBanHopBMController::class, 'store'])->name('tbm.dsbienban.store');
        Route::get('/dsbienban/edit/{id}', [DSBienBanHopBMController::class, 'edit'])->name('tbm.dsbienban.edit');
        Route::put('/dsbienban/update/{id}', [DSBienBanHopBMController::class, 'update'])->name('tbm.dsbienban.update');
        Route::delete('/dsbienban/destroy/{id}', [DSBienBanHopBMController::class, 'destroy'])->name('tbm.dsbienban.destroy');
        Route::post('/dsbienban/{id}/upload-noi-dung', [DSBienBanHopBMController::class, 'uploadNoiDung'])->name('tbm.dsbienban.upload-noi-dung');
        Route::get('/dsbienban/{id}/edit-so-gio', [DSBienBanHopBMController::class, 'editSoGio'])->name('tbm.dsbienban.edit-so-gio');
        Route::put('/dsbienban/{id}/update-so-gio', [DSBienBanHopBMController::class, 'updateSoGio'])->name('tbm.dsbienban.update-so-gio');
        Route::get('/dsbienban/{id}/download', [DSBienBanHopBMController::class, 'download'])->name('tbm.dsbienban.download');
        Route::post('/dsbienban/{id}/send-notification', [DSBienBanHopBMController::class, 'sendNotification'])->name('tbm.dsbienban.send-notification');
        
       
        Route::get('matran/index', [\App\Http\Controllers\TBM\MatranController::class, 'index'])->name('tbm.matran.index');
        Route::get('matran/create', [\App\Http\Controllers\TBM\MatranController::class, 'create'])->name('tbm.matran.create');
        Route::post('matran', [\App\Http\Controllers\TBM\MatranController::class, 'store'])->name('tbm.matran.store');
        Route::get('matran/{id}', [\App\Http\Controllers\TBM\MatranController::class, 'show'])->name('tbm.matran.show');
        Route::get('matran/{id}/edit', [\App\Http\Controllers\TBM\MatranController::class, 'edit'])->name('tbm.matran.edit');
        Route::put('matran/{id}', [\App\Http\Controllers\TBM\MatranController::class, 'update'])->name('tbm.matran.update');
        Route::delete('matran/{id}', [\App\Http\Controllers\TBM\MatranController::class, 'destroy'])->name('tbm.matran.destroy');
        Route::get('tbm/matran/{id}/export', [\App\Http\Controllers\TBM\MatranController::class, 'export'])->name('tbm.matran.export');
        Route::get('tbm/matran/{id}/export-download', [App\Http\Controllers\TBM\MatranController::class, 'exportDownload'])->name('tbm.matran.export-download');
        Route::get('tbm/matran/{id}/export-download-full', [App\Http\Controllers\TBM\MatranController::class, 'exportDownloadFull'])->name('tbm.matran.export-download-full');
        Route::get('tbm/matran/{id}/export-download-simple', [App\Http\Controllers\TBM\MatranController::class, 'exportDownloadSimple'])->name('tbm.matran.export-download-simple');
    });
    Route::middleware('auth')->group(function () {
        Route::get('/admin/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::patch('/admin/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
        Route::delete('/admin/profile', [ProfileController::class, 'destroy'])->name('admin.profile.destroy');
    });

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth'])->name('dashboard');

    // Route::middleware('auth')->group(function () {
    //     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    //     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    //     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // });

    // Quality Office Routes

    Route::prefix('qlo')->middleware(['auth', 'role:Nhân viên P.ĐBCL'])->group(function () {
        //notice
        Route::get('/notice/create', [QualityOfficerController::class, 'create'])->name('qlo.notice.create');
        Route::post('/notice/store', [QualityOfficerController::class, 'store'])->name('qlo.notice.store');
        Route::get('/notice/show/{id}', [QualityOfficerController::class, 'show'])->name('qlo.notice.show');
        Route::get('/notice/index', [QualityOfficerController::class, 'index'])->name('qlo.notice.index');
    });
    Route::prefix('quality')->name('quality.')->middleware(['auth', 'role:Admin'])->group(function () {
        Route::get('/dsdangky', [App\Http\Controllers\QualityOffice\DSDangKyController::class, 'index'])->name('dsdangky.index');
        
        Route::get('/ctdsdangky/{id_ds_dang_ky}', [App\Http\Controllers\QualityOffice\CTDSDangKyController::class, 'index'])->name('ctdsdangky.index');

        Route::put('/ctdsdangky/status/{id}', [App\Http\Controllers\QualityOffice\CTDSDangKyController::class, 'updateStatus'])->name('ctdsdangky.updateStatus');

        Route::post('/ctdsdangky/submit/{id_ds_dang_ky}', [App\Http\Controllers\QualityOffice\CTDSDangKyController::class, 'submit'])->name('ctdsdangky.submit');

        Route::put('/ctdsdangky/{id}/update-status', [App\Http\Controllers\QualityOffice\CTDSDangKyController::class, 'updateStatus'])
            ->name('ctdsdangky.updateStatus');
            
        Route::put('/ctdsdangky/{dsdangky_id}/update-status-all', [App\Http\Controllers\QualityOffice\CTDSDangKyController::class, 'updateStatusAll'])
            ->name('ctdsdangky.updateStatusAll');
            
        // Danh sách biên bản
        Route::get('/dsbienban', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'index'])->name('dsbienban.index');
        Route::get('/dsbienban/{bienban}', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'show'])->name('dsbienban.show');
        Route::get('/dsbienban/{bienban}/approve', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'approve'])->name('dsbienban.approve');
        Route::get('/dsbienban/{bienban}/reject', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'reject'])->name('dsbienban.reject');
        Route::get('/dsbienban/{bienban}/download', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'download'])->name('dsbienban.download');
        Route::post('/dsbienban/{bienban}/approve-with-email', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'approveWithEmail'])->name('dsbienban.approve-with-email');
        Route::post('/dsbienban/{bienban}/reject-with-email', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'rejectWithEmail'])->name('dsbienban.reject-with-email');
        
        // Quản lý thông báo
        Route::get('/thongbao', [App\Http\Controllers\QualityOffice\ThongbaoController::class, 'index'])->name('thongbao.index');
        Route::get('/thongbao/create', [App\Http\Controllers\QualityOffice\ThongbaoController::class, 'create'])->name('thongbao.create');
        Route::post('/thongbao/store', [App\Http\Controllers\QualityOffice\ThongbaoController::class, 'store'])->name('thongbao.store');
        Route::get('/thongbao/{thongbao}', [App\Http\Controllers\QualityOffice\ThongbaoController::class, 'show'])->name('thongbao.show');
        
        // Thống kê
        Route::get('/thongke', [QualityThongKeController::class, 'index'])->name('thongke.index');
        Route::get('/thongke/excel', [QualityThongKeController::class, 'exportExcel'])->name('thongke.excel');

        // Thống kê giảng viên
        Route::get('/thongke-giang-vien', [QualityThongKeGiangVienController::class, 'index'])->name('thongke_giang_vien.index');
        Route::get('/thongke-giang-vien/excel', [QualityThongKeGiangVienController::class, 'exportExcel'])->name('thongke_giang_vien.excel');

        // Thống kê học phần
        Route::get('/thongkehocphan', [ThongKeHocPhanController::class, 'index'])->name('thongkehocphan.index');
        Route::get('/thongkehocphan/excel', [ThongKeHocPhanController::class, 'exportExcel'])->name('thongkehocphan.excel');
    });
    Route::prefix('quality')->name('quality.')->middleware(['auth', 'permission:Duyệt danh sách đăng ký'])->group(function () {
        Route::get('/dsdangky', [App\Http\Controllers\QualityOffice\DSDangKyController::class, 'index'])->name('dsdangky.index');
        
        Route::get('/ctdsdangky/{id_ds_dang_ky}', [App\Http\Controllers\QualityOffice\CTDSDangKyController::class, 'index'])->name('ctdsdangky.index');

        Route::put('/ctdsdangky/status/{id}', [App\Http\Controllers\QualityOffice\CTDSDangKyController::class, 'updateStatus'])->name('ctdsdangky.updateStatus');

        Route::post('/ctdsdangky/submit/{id_ds_dang_ky}', [App\Http\Controllers\QualityOffice\CTDSDangKyController::class, 'submit'])->name('ctdsdangky.submit');

        Route::put('/ctdsdangky/{id}/update-status', [App\Http\Controllers\QualityOffice\CTDSDangKyController::class, 'updateStatus'])
            ->name('ctdsdangky.updateStatus');
            
        Route::put('/ctdsdangky/{dsdangky_id}/update-status-all', [App\Http\Controllers\QualityOffice\CTDSDangKyController::class, 'updateStatusAll'])
            ->name('ctdsdangky.updateStatusAll');
            
        // Danh sách biên bản
        Route::get('/dsbienban', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'index'])->name('dsbienban.index');
        Route::get('/dsbienban/{bienban}', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'show'])->name('dsbienban.show');
        Route::get('/dsbienban/{bienban}/approve', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'approve'])->name('dsbienban.approve');
        Route::get('/dsbienban/{bienban}/reject', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'reject'])->name('dsbienban.reject');
        Route::get('/dsbienban/{bienban}/download', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'download'])->name('dsbienban.download');
        Route::post('/dsbienban/{bienban}/approve-with-email', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'approveWithEmail'])->name('dsbienban.approve-with-email');
        Route::post('/dsbienban/{bienban}/reject-with-email', [App\Http\Controllers\QualityOffice\DSBienBanController::class, 'rejectWithEmail'])->name('dsbienban.reject-with-email');
        
        // Quản lý thông báo
        Route::get('/thongbao', [App\Http\Controllers\QualityOffice\ThongbaoController::class, 'index'])->name('thongbao.index');
        Route::get('/thongbao/create', [App\Http\Controllers\QualityOffice\ThongbaoController::class, 'create'])->name('thongbao.create');
        Route::post('/thongbao/store', [App\Http\Controllers\QualityOffice\ThongbaoController::class, 'store'])->name('thongbao.store');
        Route::get('/thongbao/{thongbao}', [App\Http\Controllers\QualityOffice\ThongbaoController::class, 'show'])->name('thongbao.show');
        
        // Thống kê
        Route::get('/thongke', [QualityThongKeController::class, 'index'])->name('thongke.index');
        Route::get('/thongke/excel', [QualityThongKeController::class, 'exportExcel'])->name('thongke.excel');

        // Thống kê giảng viên
        Route::get('/thongke-giang-vien', [QualityThongKeGiangVienController::class, 'index'])->name('thongke_giang_vien.index');
        Route::get('/thongke-giang-vien/excel', [QualityThongKeGiangVienController::class, 'exportExcel'])->name('thongke_giang_vien.excel');

        // Thống kê học phần
        Route::get('/thongkehocphan', [ThongKeHocPhanController::class, 'index'])->name('thongkehocphan.index');
        Route::get('/thongkehocphan/excel', [ThongKeHocPhanController::class, 'exportExcel'])->name('thongkehocphan.excel');
    });
    // Routes cho Trưởng Khoa
    Route::middleware(['auth', 'role:Trưởng Khoa'])->prefix('tk')->name('tk.')->group(function () {
        Route::controller(DSBienBanHopKhoaController::class)->prefix('dsbienban')->name('dsbienban.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::get('/dsbienban/{id}/download', [DSBienBanHopKhoaController::class, 'download'])->name('download');
            Route::get('/dsbienban/edit/{id}', [DSBienBanHopKhoaController::class, 'edit'])->name('edit');
            Route::put('/dsbienban/update/{id}', [DSBienBanHopBMController::class, 'update'])->name('update');

            Route::post('/{id}/upload-noi-dung', [DSBienBanHopKhoaController::class, 'uploadNoiDung'])->name('upload-noi-dung');
            Route::get('/{id}/edit-so-gio', [DSBienBanHopKhoaController::class, 'editSoGio'])->name('edit-so-gio');
            Route::put('/{id}/update-so-gio', [DSBienBanHopKhoaController::class, 'updateSoGio'])->name('update-so-gio');
            Route::get('/{id}/download', [DSBienBanHopKhoaController::class, 'download'])->name('download');
        });

        // Route cho danh sách đăng ký khoa
        Route::controller(DSDangKyKhoaController::class)->prefix('dsdangky')->name('dsdangky.')->group(function () {
            Route::get('/', 'index')->name('index');
            
           
        });
    });

    
    Route::prefix('cauhoi')->middleware(['auth'])->name('cauhoi.')->group(function () {
        // Danh sách học phần tham gia biên soạn
        Route::get('/hocphan', [CauHoiController::class, 'danhSachHocPhan'])->name('hocphan');
        
        // Danh sách câu hỏi của một học phần
        Route::get('/hocphan/{id}', [CauHoiController::class, 'danhSachCauHoi'])->name('danhsach');
        
        // Tạo câu hỏi mới
        Route::get('/tao/{id}', [CauHoiController::class, 'tao'])->name(name: 'tao');
        Route::post('/luu', [CauHoiController::class, 'luu'])->name('luu');
        Route::get('/import/{id}', [CauHoiController::class, 'import'])->name('import');
        Route::post('/import/upload', [CauHoiController::class, 'upload'])->name('upload');
        
        // Thêm routes mới
        Route::get('/chitiet/{id}', [CauHoiController::class, 'chiTiet'])->name('chitiet');
        Route::get('/sua/{id}', [CauHoiController::class, 'sua'])->name('sua');
        Route::post('/capnhat/{id}', [CauHoiController::class, 'capNhat'])->name('capnhat');
        Route::delete('/xoa/{id}', [CauHoiController::class, 'xoa'])->name('xoa');
        Route::get('/download-mau-import/{type}', [CauHoiController::class, 'downloadMauImport'])
            ->name('download_mau_import');
        Route::get('/download-template/{id}', [CauHoiController::class, 'downloadTemplate'])->name('download_template');
    });
    Route::get('/blank', function () {
        return Inertia::render('Blank');
    })->name('blank');  

    require __DIR__.'/auth.php';
