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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $institution = $request->attributes->get('institution');
        $institutionId = $institution?->id;

        $allowed = $request->user()?->roles()
            ->whereIn('roles.slug', $roles)
            ->where(function ($query) use ($institutionId) {
                $query->whereNull('user_roles.institution_id')
                    ->orWhere('user_roles.institution_id', $institutionId);
            })
            ->exists();

        abort_unless($allowed, 403, 'You do not have permission to access this area.');

        return $next($request);
    }
}
