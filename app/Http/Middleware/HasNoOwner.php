<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasNoOwner
{
   /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! User::role('owner')->count() || User::role('owner')->count() === 0) {
            // Redirect to a specific route if there is no owner
            return redirect()->route('owner.auth.register');
        }
        return $next($request);
    }
}