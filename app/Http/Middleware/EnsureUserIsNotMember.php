<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->is_member) { // Check the 'is_member' flag on User model
            // Or check roles: if (Auth::check() && Auth::user()->hasRole('Member')) {
            return redirect()->route('frontend.membership.application.status')->with('info', 'You are already a member or your application is being processed.');
        }
        return $next($request);
    }
}
