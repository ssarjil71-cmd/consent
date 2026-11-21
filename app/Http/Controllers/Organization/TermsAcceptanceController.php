<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;

class TermsAcceptanceController extends Controller
{
    /**
     * Show terms acceptance form
     */
    public function show()
    {
        $organization = auth('organization')->user();
        $orgType = $organization->type;
        
        if (!$orgType || (!$orgType->terms_text && !$orgType->liability_release_text && !$orgType->risk_acknowledgement_text)) {
            return redirect()->route('org.dashboard');
        }

        return view('organization.terms.acceptance', compact('organization', 'orgType'));
    }

    /**
     * Handle terms acceptance
     */
    public function accept(Request $request)
    {
        $request->validate([
            'accept_all_terms' => 'required|accepted',
        ]);

        $organization = auth('organization')->user();
        
        $organization->update([
            'terms_accepted_at' => now(),
            'liability_accepted_at' => now(),
        ]);

        return redirect()
            ->route('org.dashboard')
            ->with('success', 'Terms and conditions accepted successfully!');
    }
}
