<?php

namespace App\Providers;

use App\Repositories\CXRepository;
use App\Repositories\Interfaces\CXrepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CXrepositoryInterface::class, CXRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
