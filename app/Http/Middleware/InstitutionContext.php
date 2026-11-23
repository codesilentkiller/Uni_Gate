<?php

namespace App\Http\Middleware;

use App\Models\Institution;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstitutionContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $institution = $request->route('institution');

        if (! $institution instanceof Institution) {
            $institution = Institution::where('slug', $institution)->where('status', 'active')->first();
        }

        abort_if(! $institution, 404, 'Institution not found or inactive.');

        $user = $request->user();
        abort_unless(
            $user && $institution->users()->whereKey($user->id)->wherePivot('status', 'active')->exists(),
            403,
            'You are not authorized to access this institution.'
        );

        $request->attributes->set('institution', $institution);
        $request->merge(['institution_id' => $institution->id]);

        return $next($request);
    }
}
