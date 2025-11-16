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
        // Not authenticated -> redirect home
        if (! $user) {
            return redirect('/');
        }

        // Authenticated -> route by role
        if ($user->hasRole(Role::Owner->value)) {
            return to_route('owner.dashboard');
        }

        if ($user->hasRole(Role::Tenant->value)) {
            return to_route('tenant.dashboard');
        }

        // Authenticated but no matching role -> redirect home (or handle differently)
        return redirect('/');
    }
}
