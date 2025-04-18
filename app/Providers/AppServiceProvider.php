<?php

namespace App\Providers;

use App\Factories\LineClientFactory;
use Illuminate\Support\ServiceProvider;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Api\MessagingApiBlobApi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(MessagingApiApi::class, function () {
            return LineClientFactory::messaging();
        });

        $this->app->singleton(MessagingApiBlobApi::class, function () {
            return LineClientFactory::blob();
        });
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
