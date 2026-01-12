<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || $request->user()->rol !== $role) {
            // If the user is an admin trying to access patient routes, redirect to admin dashboard?
            // Or just generic 403. For now, 403 is safer.
            abort(403, 'Acceso denegado. No tienes el rol necesario.');
        }

        return $next($request);
    }
}
