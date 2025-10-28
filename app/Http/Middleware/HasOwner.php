<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasOwner
{
   /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (User::role('owner')->count() >= 1) {
            // Redirect to a specific route if there is already an owner
            return redirect()->route('owner.auth.login');
        }
        return $next($request);
    }
}