<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Accounts created by an administrator start with a temporary password.
 * Until it is changed, the user can only reach the profile page and log out.
 */
class EnsurePasswordIsChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password && ! $request->routeIs('profile.*', 'logout')) {
            return redirect()->route('profile.show')
                ->with('warning', 'For your security, please set a new password before continuing.');
        }

        return $next($request);
    }
}
