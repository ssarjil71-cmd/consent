<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

class ActivationController extends Controller
{
    /**
     * Show activation page
     */
    public function show()
    {
        $organization = Auth::guard('organization')->user();
        
        // If already active, redirect to dashboard
        if ($organization->status === 'active') {
            return redirect()->route('org.dashboard');
        }
        
        return view('organization.activation.show', compact('organization'));
    }
    
    /**
     * Process payment activation
     */
    public function payment(Request $request)
    {
        $organization = Auth::guard('organization')->user();
        
        $request->validate([
            'payment_method' => 'required|in:razorpay',
            'razorpay_payment_id' => 'required_if:payment_method,razorpay|string',
        ]);
        
        // Update payment details
        $organization->update([
            'paid_amount' => $organization->activation_fee,
            'payment_status' => 'paid',
            'payment_id' => $request->razorpay_payment_id,
            'payment_date' => now(),
            'status' => 'active', // Activate the organization
        ]);
        
        return redirect()->route('org.activation.success')
            ->with('success', 'Your account has been activated successfully!');
    }
    
    /**
     * Show activation success page
     */
    public function success()
    {
        $organization = Auth::guard('organization')->user();
        
        // If not active, redirect to activation page
        if ($organization->status !== 'active') {
            return redirect()->route('org.activation.show');
        }
        
        return view('organization.activation.success', compact('organization'));
    }
}
