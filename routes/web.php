<?php

use App\Enums\Role;
use App\Http\Controllers\HomeRouteController;
use App\Livewire\Actions\Logout;
use App\Livewire\Tenant\Auth\Login as TenantLogin;
use App\Livewire\Tenant\Auth\Register as TenantRegister;
use App\Livewire\Owner\Auth\Login as OwnerLogin;
use App\Livewire\Owner\Auth\Register as OwnerRegister;
use App\Livewire\Owner\Pages\Dashboard as OwnerDashboard;
use Illuminate\Support\Facades\Route;

/*
*-----------------------------------------
*             COMMON ROUTES
*-----------------------------------------
*/

Route::get('home', HomeRouteController::class)
    ->name('home');

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', function () {
    return view('welcome');
})->name('login');

// TERMS AND CONDITIONS
Route::get('/terms-and-conditions', function () {
    return view('common.terms-and-conditions');
})->name('terms-and-conditions');

// LOGOUT
Route::post('logout', Logout::class)->name('logout');



/*
*-----------------------------------------
*           OWNER ROUTES
*-----------------------------------------
*/

Route::group([
    'middleware' => ['web'],
    'prefix' => 'owner',
    'as' => 'owner.',
], function () {

    /*
    *-----------------------------------------
    *           OWNER GUEST ROUTES
    *-----------------------------------------
    */

    Route::group([
        // 'middleware' => ['guest', 'throttle:10,1'],
        'prefix' => 'auth',
        'as' => 'auth.',
    ], function () {
        Route::get('login', OwnerLogin::class)->middleware('has_no_owner')->name('login');
        Route::get('register', OwnerRegister::class)->middleware('has_owner')->name('register');
    });

    /*
    *-----------------------------------------
    *       OWNER AUTHENTICATED ROUTES
    *-----------------------------------------
    */
    Route::group([
        'middleware' => [
            'auth',
            'has_no_owner',
            'role:'.Role::Owner->value,
        ],
    ], function () {

        /*
        *-----------------------------------------
        *             SIDEBAR ROUTES
        *-----------------------------------------
        */

        // DASHBOARD
        Route::get('dashboard', OwnerDashboard::class)
            ->name('dashboard');

        /*
        *-----------------------------------------
        *              HEADER ROUTES
        *-----------------------------------------
        */

        // PROFILE
        // Route::get('profile', Profile::class)
        //     ->name('profile');
    });
});



/*
*-----------------------------------------
*             TENANT ROUTES
*-----------------------------------------
*/

Route::group([
    'middleware' => 'web',
    'prefix' => 'tenant',
    'as' => 'tenant.',
], function () {

    /*
    *-----------------------------------------
    *           TENANT GUEST ROUTES
    *-----------------------------------------
    */

    Route::group([
        'middleware' => ['guest', 'throttle:10,1'],
        'prefix' => 'auth',
        'as' => 'auth.',
    ], function () {
        Route::get('login', TenantLogin::class)->name('login');
        Route::get('register', TenantRegister::class)->name('register');
    });

    /*
    *-----------------------------------------
    *       TENANT AUTHENTICATED ROUTES
    *-----------------------------------------
    */
    Route::group([
        'middleware' => [
            'auth',
            'role:'.Role::Tenant->value,
        ],
    ], function () {

        /*
        *-----------------------------------------
        *             SIDEBAR ROUTES
        *-----------------------------------------
        */

        // DASHBOARD
        // Route::get('dashboard', TenantDashboard::class)
        //     ->name('dashboard');

        /*
        *-----------------------------------------
        *              HEADER ROUTES
        *-----------------------------------------
        */

        // PROFILE
        // Route::get('profile', Profile::class)
        //     ->name('profile');
    });
});