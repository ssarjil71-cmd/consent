<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // For admin routes
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            if ($request->routeIs('org.*')) {
                $organizationId = $request->route('organization');
                // Fallback for routes that might not have the parameter in the URL directly
                if (!$organizationId && session('organization_id')) {
                    $organizationId = session('organization_id');
                }
                
                if ($organizationId) {
                    return route('org.login', ['organization' => $organizationId]);
                }
                // Redirect to a generic error or homepage if no organization context is found
                return route('home'); 
            }
            // Default login route
            return route('login');
        }
    }

    protected function authenticate($request, array $guards)
    {
        if ($this->auth->guard($guards[0])->check()) {
            return $this->auth->shouldUse($guards[0]);
        }

        $this->unauthenticated($request, $guards);
    }
}
