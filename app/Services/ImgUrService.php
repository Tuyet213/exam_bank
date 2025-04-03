<?php

namespace App\Services;
use Exception;
use GuzzleHttp\Client;

class ImgUrService
{
    public function uploadImage($image)
    {
        $client = new Client();
        $response = $client->request('POST', 'https://api.imgur.com/3/image', [
            'headers' => [
                'Authorization' => 'Client-ID ' . env('IMGUR_CLIENT_ID'),
            ],
            'multipart' => [
                [
                    'name' => 'image',
                    'contents' => fopen($image, 'r'),
                ],
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['data']['link'];
    }
}

?>