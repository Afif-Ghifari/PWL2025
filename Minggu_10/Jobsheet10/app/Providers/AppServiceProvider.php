<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
        // mengambil data user yang telah login dan menampilkannya ke sidebar
        View::composer('layouts.sidebar', function ($view) {
            $view->with('authUser', Auth::user());
        });
    }
}
