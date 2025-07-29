<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Coordinator;
use App\Models\Event;
use App\Models\Course;
use App\Observers\CoordinatorObserver;


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
        // Chamando a observer do coordenador para funcionar
        Coordinator::observe(CoordinatorObserver::class);
    }
}
