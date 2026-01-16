<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Google\Client;
use Google\Service\Drive;
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
        \Illuminate\Support\Facades\Gate::define('viewPulse', function ($user) {
            return $user->hasRole('Admin');
        });

        Storage::extend('google', function ($app, $config) {
            $client = new Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);

            $token = $client->fetchAccessTokenWithRefreshToken($config['refreshToken']);

            if (isset($token['error'])) {
                \Illuminate\Support\Facades\Log::error('Google Drive Token Error:', $token);
                throw new \Exception('Google Drive Token Error: ' . ($token['error_description'] ?? $token['error']));
            }

            $client->setAccessToken($token);

            $service = new Drive($client);
            $adapter = new GoogleDriveAdapter($service, $config['folder'] ?? '/');
            $driver = new Filesystem($adapter);

            return new FilesystemAdapter($driver, $adapter, $config);
        });
    }
}
