<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NoticeMail;
use App\Models\ThongBao;
use Illuminate\Support\Facades\Storage;
use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\Permission as Google_Service_Drive_Permission;

//New code

class QualityOfficerController extends Controller
{
    public function index()
    {
        $query = ThongBao::where('able', true);
        $thongbaos = $query->paginate(10)->withQueryString();
        return Inertia::render('QualityOfficer/Index', compact('thongbaos'));
    }

    public function show($id)
    {
        $thongbao = ThongBao::find($id);
        $thongbao->files = json_decode($thongbao->files);
        return Inertia::render('QualityOfficer/Show', compact('thongbao'));
    }

    public function create()
    {
        return Inertia::render('QualityOfficer/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'files.*' => 'nullable|file'
        ]);

         // Gửi email thông báo
    //      $receivers = User::where('able', true)
    //      ->whereHas('roles', function ($query) {
    //          $query->where('name', 'TK');
    //      })
    //      ->get();

    //  foreach ($receivers as $receiver) {
    //      Mail::to($receiver->email)->send(new NoticeMail($request->title, $request->content, $request->file('files')));
    //  }

    
    $fileUrls = [];
    if ($request->hasFile('files')) {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('credentials/google-drive.json'));
        $client->addScope(Google_Service_Drive::DRIVE);
        $service = new Google_Service_Drive($client);

        $files = $request->file('files');
        foreach ($files as $file) {
            $filePath = Storage::disk('google')->putFile(env('GOOGLE_DRIVE_FOLDER_ID'), $file);

            if ($filePath) {
                $fileId = explode("/", $filePath);
                $fileId = end($fileId); 
                echo $fileId;
                $permission = new Google_Service_Drive_Permission();
                $permission->setRole('reader'); // Quyền xem
                $permission->setType('anyone'); // Bất kỳ ai có link
                $service->permissions->create($fileId, $permission);

                $fileUrl = "https://drive.google.com/uc?id={$fileId}";
                $fileName = $file->getClientOriginalName();
                $fileUrls[$fileName] = $fileUrl;

            }
        }
    }

        //dd(Storage::disk('google')->allFiles());
    ThongBao::create([
        'title' => $request->title,
        'content' => $request->content,
        'files' => json_encode($fileUrls),
    ]);
    return redirect()->back()->with('success', 'Thông báo đã được lưu thành công');
      
    }
}