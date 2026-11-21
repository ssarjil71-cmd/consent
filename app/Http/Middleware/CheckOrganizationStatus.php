<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

class CheckOrganizationStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('organization')->check()) {
            $organization = Auth::guard('organization')->user();
            
            // Check if organization is inactive
            if ($organization->status === 'inactive') {
                // Allow only activation routes
                $allowedRoutes = [
                    'org.activation.show',
                    'org.activation.payment',
                    'org.activation.success',
                    'org.logout'
                ];
                
                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('org.activation.show');
                }
            }
        }
        
        return $next($request);
    }
}
