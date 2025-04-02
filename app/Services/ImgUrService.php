<?php

namespace App\Services;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ImgUrService
{
    public function uploadImage($image)
    {
        try {
            $client = new Client();
            $clientId = env('IMGUR_CLIENT_ID');
            $clientSecret = env('IMGUR_CLIENT_SECRET');
            $redirectUri = env('IMGUR_REDIRECT_URI');
            $response = $client->request('POST', 'https://api.imgur.com/3/image', [
                'headers' => [
                    'Authorization' => 'Client-ID ' . $clientId,
                ],
                'multipart' => [
                    [
                        'name' => 'image',
                        'contents' => fopen($image->getPathname(), 'r'),
                    ]
                ],
                'connect_timeout' => 30,
                'timeout' => 30,
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if (isset($data['data']['link'])) {
                // Đảm bảo link ảnh sử dụng https
                $link = $data['data']['link'];
                if (strpos($link, 'http://') === 0) {
                    $link = 'https://' . substr($link, 7);
                }
                
                return $link;
            }
            
            Log::error('Imgur response không chứa link: ', $data);
            return null;
        } catch (Exception $e) {
            Log::error('Lỗi upload ảnh imgur: ' . $e->getMessage());
            return null;
        }
    }
}

?>