<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Location;
use App\Models\QuestionPackage;
use App\Models\User;
use App\Policies\EventPolicy;
use App\Policies\LocationPolicy;
use App\Policies\QuestionPackagePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Event::class, EventPolicy::class);
        Gate::policy(Location::class, LocationPolicy::class);
        Gate::policy(QuestionPackage::class, QuestionPackagePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
