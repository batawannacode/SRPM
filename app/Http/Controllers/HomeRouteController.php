<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Enums\Role;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class HomeRouteController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();
        return match (true) {
            $user->hasRole(Role::Owner->value) => to_route('owner.dashboard'),
            $user->hasRole(Role::Tenant->value) => to_route('tenant.dashboard'),
            default => redirect('/'),
        };
    }
}