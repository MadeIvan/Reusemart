<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use App\Services\FCMService;
use GuzzleHttp\Client;             // Tambah ini
use GuzzleHttp\ClientInterface;    // Tambah ini

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Binding GuzzleHttp ClientInterface ke Client agar bisa resolve dependency
        $this->app->bind(ClientInterface::class, Client::class);

        // Registrasi singleton untuk Firebase Messaging
        $this->app->singleton(Messaging::class, function ($app) {
            $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
            return $factory->createMessaging();
        });

        // Registrasi singleton untuk FCMService, agar bisa di-inject di controller
        $this->app->singleton(FCMService::class, function ($app) {
            return new FCMService($app->make(Messaging::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
