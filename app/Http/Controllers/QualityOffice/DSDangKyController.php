<?php

namespace App\Http\Controllers\QualityOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\DSDangKy;
use App\Models\CTDSDangKy;

class DSDangKyController extends Controller
{
    public function index()
    {
        $dsDangKy = DSDangKy::with(['boMon', 'ctDSDangKies'])->get()->sortByDesc('created_at');
        
        // Xử lý trạng thái cho từng DSDangKy
        $dsDangKy->each(function ($ds) {
            if ($ds->ctDSDangKies->isEmpty()) {
                $ds->status = 'Pending';
            } else if ($ds->ctDSDangKies->contains('trang_thai', 'Rejected')) {
                $ds->status = 'Rejected';
            } else if ($ds->ctDSDangKies->every(function ($ct) {
                return $ct->trang_thai === 'Approved';
            })) {
                $ds->status = 'Approved';
            } else {
                $ds->status = 'Pending';
            }
        });

        return Inertia::render('QualityOffice/DSDangKy/Index', [
            'dsdangky' => $dsDangKy
        ]);
    }
} 