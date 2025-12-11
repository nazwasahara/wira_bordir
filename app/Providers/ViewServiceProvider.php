<?php

namespace App\Providers;

use App\Http\View\Composers\OwnerNavbarComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Owner Navbar Composer
        View::composer('partials.owner.navbar', OwnerNavbarComposer::class);
    }
}
