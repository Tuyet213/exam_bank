<?php

namespace App\Http\Controllers\QualityOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NoticeMail;
use App\Models\ThongBao;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use App\Services\ImgUrService;
//New code

class QualityOfficerController extends Controller
{
    public function index()
    {
        $query = ThongBao::where('able', true);
        $thongbaos = $query->paginate(10)->withQueryString();
        
        // Thêm trường formatted_date thay vì thay đổi created_at
        $thongbaos->through(function ($thongbao) {
            $thongbao->formatted_date = date('d/m/Y H:i', strtotime($thongbao->created_at));
            return $thongbao;
        });
        
        return Inertia::render('QualityOffice/QualityOfficer/Index', compact('thongbaos'));
    }

    public function show($id)
    {
        $thongbao = ThongBao::find($id);
        $thongbao->files = json_decode($thongbao->files);
        $imgUrService = new ImgUrService();
        return Inertia::render('QualityOffice/QualityOfficer/Show', compact('thongbao'));
    }

    public function create()
    {
        return Inertia::render('QualityOffice/QualityOfficer/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240'
        ]);

         // Gửi email thông báo
         $receivers = User::where('able', true)
         ->whereHas('roles', function ($query) {
             $query->where('name', 'TK');
         })
         ->get();

     foreach ($receivers as $receiver) {
         Mail::to($receiver->email)->send(new NoticeMail($request->title, $request->content, $request->file('files')));
     }

    
    $fileUrls = [];
    if ($request->hasFile('files')) {
        $imgUrService = new ImgUrService();
        foreach ($request->file('files') as $file) {
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $fileUrl = $imgUrService->uploadImage($file);
            $fileUrls[] = ['url' => $fileUrl, 'name' => $fileName];
        }
    }

    ThongBao::create([
        'title' => $request->title,
        'content' => $request->content,
        'files' => json_encode($fileUrls),
    ]);
    
    return redirect()->route('qlo.notice.index')->with('success', 'Thông báo đã được lưu thành công');
      
    }
}