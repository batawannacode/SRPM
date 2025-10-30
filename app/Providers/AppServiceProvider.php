<?php

namespace App\Providers;

use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\View;
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
        // Share user and super admin/admin data with all views
        View::composer('*', function ($view) {
            $user = auth()->user();
            if ($user) {
                if ($user->hasAnyRole(['owner', 'tenant'])) {
                    $view->with([
                        'user' => $user,
                        'owner' => $user->owner,
                        'tenant' => $user->tenant,
                    ]);
                }
            }
        });

        // Password Requirement
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });
    }
}
