<?php

namespace App\Http\Controllers\QualityOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\DSDangKy;
use App\Models\CTDSDangKy;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyTBMReview;
use App\Models\User;

class CTDSDangKyController extends Controller
{
    public function index($id_ds_dang_ky)
    {
        $dsDangKy = DSDangKy::with('boMon')->where('able', true)->findOrFail($id_ds_dang_ky);
        $ctDsDangKy = CTDSDangKy::with(['hocPhan', 'dsGVBienSoans.vienChuc'])
            ->where('id_ds_dang_ky', $id_ds_dang_ky)
            ->where('able', true)
            ->get();

        return Inertia::render('QualityOffice/CTDSDangKy/Index', [
            'dsdangky' => $dsDangKy,
            'ctdsdangky' => $ctDsDangKy
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $ctDsDangKy = CTDSDangKy::where('able', true)->findOrFail($id);
        $ctDsDangKy->update([
            'trang_thai' => $request->trang_thai
        ]);

        return redirect()->route('quality.ctdsdangky.index', $request->dsdangky_id)->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function updateStatusAll($dsdangky_id, Request $request)
    {
        $ctDSDangKies = CTDSDangKy::where('id_ds_dang_ky', $dsdangky_id)
            ->where('able', true)
            ->whereIn('trang_thai', ['Draft', 'Rejected', 'Pending'])
            ->get();

        foreach ($ctDSDangKies as $ctDSDangKy) {
            $ctDSDangKy->trang_thai = $request->trang_thai;
            $ctDSDangKy->save();
        }

        return redirect()->route('quality.ctdsdangky.index', $dsdangky_id)->with('success', 'Cập nhật trạng thái tất cả thành công!');
    }

    public function submit($id_ds_dang_ky)
    {
        try {
            $dsDangKy = DSDangKy::with(['boMon'])->where('able', true)->findOrFail($id_ds_dang_ky)->first();
            

            // Lấy email của trưởng bộ môn
            $emailTBM = User::where('id_bo_mon', $dsDangKy->id_bo_mon)->where('able', true)->whereHas('roles', function($query) {
                $query->where('name', 'Trưởng Bộ Môn');
            })->first()->email;
            
            // Gửi mail
            Mail::to($emailTBM)->send(new NotifyTBMReview($dsDangKy));

            return redirect()->route('quality.ctdsdangky.index', $id_ds_dang_ky)->with('success', 'Đã gửi thông báo cho Trưởng bộ môn!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gửi thông báo: ' . $e->getMessage());
        }
    }
} 