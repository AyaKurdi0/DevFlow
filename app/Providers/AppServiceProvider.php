<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            'Illuminate\Notifications\DatabaseNotification',
            'App\Models\Notification'
        );
    }
}
