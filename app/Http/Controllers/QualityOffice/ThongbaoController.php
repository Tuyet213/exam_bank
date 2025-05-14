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
    public function index()
    {
        $thongbaos = ThongBao::where('able', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $thongbaos->through(function ($thongbao) {
            $thongbao->formatted_date = date('d/m/Y H:i', strtotime($thongbao->created_at));
            return $thongbao;
        });

        return Inertia::render('QualityOffice/Thongbao/Index', [
            'thongbaos' => $thongbaos
        ]);
    }

    public function create()
    {
        return Inertia::render('QualityOffice/Thongbao/Create');
    }

    public function store(Request $request)
    {
       

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240'
        ]);

        $attachments = [];
        $files = $request->file('files');
        Log::info('FILES', ['files' => $files]);
        if ($files) {
            foreach (is_array($files) ? $files : [$files] as $file) {
                $fileName = uniqid() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/thongbao', $fileName);
                $attachments[] = storage_path('app/' . $filePath);
            }
        }

        $thongbao = ThongBao::create([
            'title' => $request->title,
            'content' => $request->content,
            'files' => json_encode($attachments),
            'able' => true
        ]);

        $truongBoMons = User::where('able', true)
            ->whereHas('roles', function($q) {
                $q->where('name', 'Trưởng Bộ Môn');
            })->get();

        foreach ($truongBoMons as $truongBoMon) {
            Mail::to($truongBoMon->email)
                ->send(new NoticeMail($request->title, $request->content, $attachments));
        }

        return redirect()->route('quality.thongbao.index')
            ->with('success', 'Thông báo đã được tạo và gửi email thành công đến tất cả Trưởng Bộ Môn');
    }

    public function show($id)
    {
        $thongbao = ThongBao::findOrFail($id);
        if ($thongbao->files) {
            $thongbao->files = json_decode($thongbao->files);
        }
        return Inertia::render('QualityOffice/Thongbao/Show', [
            'thongbao' => $thongbao
        ]);
    }
}
