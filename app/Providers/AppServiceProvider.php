<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Masbug\Flysystem\GoogleDriveAdapter;
use League\Flysystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);     
        
        // Đảm bảo session được start trước khi sử dụng
        if (!app()->runningInConsole() && !Session::isStarted()) {
            Session::start();
        }
        
        // Đảm bảo CSRF token luôn được tạo
        if (!Session::has('_token')) {
            Session::put('_token', Str::random(40));
        }
        
        Storage::extend('google', function ($app, $config) {
            $client = new Google_Client();
            $client->setAuthConfig(storage_path('credentials/google-drive.json'));
            $client->addScope(Google_Service_Drive::DRIVE);
        
            $service = new Google_Service_Drive($client);
            $adapter = new GoogleDriveAdapter($service, env('GOOGLE_DRIVE_FOLDER_ID'));
        
            return new FilesystemAdapter(new Filesystem($adapter), $adapter);
        });
        
    }
}
