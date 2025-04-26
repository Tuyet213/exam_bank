<?php

namespace App\Http\Controllers\QualityOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ThongBao;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NoticeMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ThongbaoController extends Controller
{
    /**
     * Hiển thị danh sách thông báo
     */
    public function index()
    {
        $thongbaos = ThongBao::where('able', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Định dạng ngày để hiển thị
        $thongbaos->through(function ($thongbao) {
            $thongbao->formatted_date = date('d/m/Y H:i', strtotime($thongbao->created_at));
            return $thongbao;
        });
        
        return Inertia::render('QualityOffice/Thongbao/Index', [
            'thongbaos' => $thongbaos
        ]);
    }

    /**
     * Hiển thị form thêm mới thông báo
     */
    public function create()
    {
        return Inertia::render('QualityOffice/Thongbao/Create');
    }

    /**
     * Lưu thông báo mới và gửi email đến các Trưởng Bộ Môn
     */
    public function store(Request $request)
    {
        Log::info('store');
        Log::info($request->all());
        try {
            Log::info('=== TRƯỚC KHI VALIDATE ===');
            $request->validate([
                'title' => 'required|string',
                'content' => 'required|string',
                'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,ppt,pptx'
            ]);
            Log::info('=== SAU KHI VALIDATE THÀNH CÔNG ===');
        } catch (\Exception $e) {
            Log::error('Lỗi khi validate: ' . $e->getMessage());
        }
        Log::info('Validated data:', $request->all());

        // Xử lý file đính kèm
        $fileUrls = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = uniqid() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/thongbao', $fileName);
                $fileUrls[] = [
                    'url' => Storage::url($filePath),
                    'name' => $fileName
                ];
            }
        }
        Log::info($fileUrls);
        // Tạo thông báo mới
        $thongbao = ThongBao::create([
            'title' => $request->title,
            'content' => $request->content,
            'files' => json_encode($fileUrls),
            'able' => true
        ]);
        Log::info($thongbao);
        // Tìm tất cả Trưởng Bộ Môn
        $truongBoMons = User::where('able', true)
            ->whereHas('roles', function($query) {
                $query->where('name', 'Trưởng Bộ Môn');
            })
            ->get();
        Log::info('truongBoMons'.$truongBoMons);
        // Gửi email thông báo
        foreach ($truongBoMons as $truongBoMon) {
            try {
                Mail::to($truongBoMon->email)
                    ->send(new NoticeMail($request->title, $request->content, $request->file('files')));
                
                Log::info('Đã gửi email thông báo đến: ' . $truongBoMon->email);
            } catch (\Exception $e) {
                Log::error('Lỗi gửi email đến ' . $truongBoMon->email . ': ' . $e->getMessage());
            }
        }
        Log::info('Đã gửi email thông báo đến tất cả Trưởng Bộ Môn');
        return redirect()->route('quality.thongbao.index')
            ->with('success', 'Thông báo đã được tạo và gửi email thành công đến tất cả Trưởng Bộ Môn');
    }

    /**
     * Hiển thị chi tiết thông báo
     */
    public function show($id)
    {
        $thongbao = ThongBao::findOrFail($id);
        
        // Decode thông tin files
        if ($thongbao->files) {
            $thongbao->files = json_decode($thongbao->files);
        }
        
        return Inertia::render('QualityOffice/Thongbao/Show', [
            'thongbao' => $thongbao
        ]);
    }
} 