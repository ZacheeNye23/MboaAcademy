<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Laravel\Socialite\Facades\Socialite;

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
    public function boot():void
{
   // Fix SSL WAMP local uniquement
        if (app()->environment('local')) {
            $certPath = 'C:\\wamp64\\bin\\php\\php8.3.14\\extras\\ssl\\cacert.pem';

            config(['services.google.guzzle' => [
                'verify' => $certPath,
            ]]);
        }

if (app()->environment('production')) {
    \Illuminate\Support\Facades\URL::forceScheme('https');
}

      $certPath = 'C:\wamp64\bin\php\php8.3.14\extras\ssl\cacert.pem';

    Socialite::extend('google', function ($app) use ($certPath) {
        $config = $app['config']['services.google'];
        return Socialite::buildProvider(
            \Laravel\Socialite\Two\GoogleProvider::class,
            $config
        )->setHttpClient(new Client(['verify' => $certPath]));
    });
    Schema::defaultStringLength(191);
}
}
