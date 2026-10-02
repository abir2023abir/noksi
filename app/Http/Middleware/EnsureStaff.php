<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isStaff() || ! $user->is_active) {
            if ($user && ! $user->is_active) {
                auth()->logout();
            }

            return redirect()->route('admin.login')->with('error', __('You do not have access to the admin area.'));
        }

        return $next($request);
    }
}
