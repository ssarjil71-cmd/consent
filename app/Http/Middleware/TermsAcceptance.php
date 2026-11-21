<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Organization;

class TermsAcceptance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('organization')->check()) {
            $organization = auth('organization')->user();
            
            // Check if organization needs to accept terms
            if ($this->needsTermsAcceptance($organization)) {
                return redirect()->route('org.terms.show');
            }
        }

        return $next($request);
    }

    /**
     * Check if organization needs to accept terms
     */
    private function needsTermsAcceptance(Organization $organization): bool
    {
        // If already accepted, no need to show
        if ($organization->terms_accepted_at && $organization->liability_accepted_at) {
            return false;
        }

        // Get organization type to check if terms are configured
        $orgType = $organization->type;
        if (!$orgType) {
            return false;
        }

        // Check if any terms are configured for this organization type
        return !empty($orgType->terms_text) || 
               !empty($orgType->liability_release_text) || 
               !empty($orgType->risk_acknowledgement_text);
    }
}
