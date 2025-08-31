<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Coordinator;
use App\Models\Event;
use App\Models\Course;
use App\Observers\CoordinatorObserver;
use App\Observers\EventObserver;
use App\Observers\CourseObserver;


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
        Coordinator::observe(CoordinatorObserver::class);

        Event::observe(EventObserver::class);

        Course::observe(CourseObserver::class);
    }
}
