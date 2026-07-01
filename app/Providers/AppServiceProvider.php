<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
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
        // Polymorphic assignee: an asset can be assigned to an Employee or a Location.
        // Use morphMap (not enforceMorphMap) so other morphs — e.g. the activity log's
        // User causer and Asset subjects — keep using their full class names.
        Relation::morphMap([
            'employee' => \App\Models\Employee::class,
            'location' => \App\Models\Location::class,
        ]);
    }
}
