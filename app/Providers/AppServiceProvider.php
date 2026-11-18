<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // One place that defines what a valid password is, used by every form.
        Password::defaults(fn () => Password::min(config('campus.password_min_length', 8))
            ->letters()
            ->numbers());
    }
}
