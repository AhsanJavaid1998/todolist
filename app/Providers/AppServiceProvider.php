<?php

namespace App\Providers;

use App\Contracts\AuthContract;
use App\Contracts\TodoContract;
use App\services\AuthService;
use App\services\TodoService;
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
        $this->app->bind(
            TodoContract::class,function ($app){
            return $app->make(TodoService::class);
        });
        $this->app->bind(
            AuthContract::class,function ($app){
            return $app->make(AuthService::class);
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
