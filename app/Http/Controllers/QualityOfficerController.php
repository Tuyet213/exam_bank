<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NoticeMail;
use App\Models\ThongBao;
use Exception;
use Google\Client;
use Google\Photos\Library\V1\PhotosLibraryClient;
use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Photos\Library\V1\PhotosLibraryResourceFactory;

//New code

class QualityOfficerController extends Controller
{
    public function create()
    {
        return Inertia::render('QualityOfficer/Create');
    }

    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
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

        
    // https://developers.google.com/photos/library/legacy/guides/get-started-php

    
    $fileUrls = [];
    if ($request->hasFile('files')) {
        $jsonKey = base_path(env('GOOGLE_APPLICATION_CREDENTIALS'));
        echo $jsonKey;
        $authCredentials = new UserRefreshCredentials(
            'https://www.googleapis.com/auth/photoslibrary',
            $jsonKey,
        );
        $photosLibraryClient = new PhotosLibraryClient(['credentials' => $authCredentials]);
        $newAlbum = PhotosLibraryResourceFactory::album("Exambank");
        $createdAlbum = $photosLibraryClient->createAlbum($newAlbum);
        $albumId = $createdAlbum->getId();
        // Tải từng tệp lên Google Photos
        foreach ($request->file('files') as $file) {
            $uploadToken = $photosLibraryClient->upload($file->getRealPath());
            $newMediaItem = $photosLibraryClient->batchCreateMediaItems([
                'newMediaItems' => [
                    [
                        'simpleMediaItem' => ['uploadToken' => $uploadToken]
                    ]   
                ]
            ]);
            $photosLibraryClient->batchCreateMediaItems($newMediaItem, ['albumId' => $albumId]);
            $fileUrls[] = $newMediaItem->getNewMediaItemResults()[0]->getMediaItem()->getBaseUrl();
            
        }
    }

    // Lưu thông tin vào bảng ThongBao
    ThongBao::create([
        'title' => $request->title,
        'content' => $request->content,
        'files' => json_encode($fileUrls),
    ]);
            return redirect()->back()->with('success', 'Thông báo đã được lưu thành công');
      
    }
}