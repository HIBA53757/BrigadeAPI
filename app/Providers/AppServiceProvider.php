<?php

namespace App\Providers;


use App\Models\Category;
use App\Models\Plat;
use App\Policies\CategoryPolicy;
use App\Policies\PlatPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Plat::class => PlatPolicy::class,
    ];
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Plat::class, PlatPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);

        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));
    }
}
