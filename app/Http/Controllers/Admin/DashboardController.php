<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Admin;
use App\Models\Consent;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        $data = [
            'todayConsent'       => Consent::whereDate('created_at', $today)->count(),
            'totalConsent'       => Consent::count(),
            'pendingConsent'     => Consent::where('status', 'pending')->count(),
            'approvedConsent'    => Consent::where('status', 'approved')->count(),
            'rejectedConsent'    => Consent::where('status', 'rejected')->count(),
            'totalOrganizations' => Organization::count(), // Removed active() scope
            'todayTransaction'   => $this->formatAmount(Organization::whereDate('created_at', $today)->sum('transaction_amount')),
            'totalTransaction'   => $this->formatAmount(Organization::sum('transaction_amount')),
            'teamMembers'        => Admin::count(), // Removed active() scope
            'newOrganizations'   => Organization::whereDate('created_at', '>=', $today->subDays(7))->count()
        ];
        
        return view('admin.dashboard', compact('data'));
    }
    
    private function formatAmount($amount)
    {
        // Ensure the amount is a number and format it with 2 decimal places
        $amount = is_numeric($amount) ? $amount : 0;
        return '₹' . number_format((float)$amount, 2);
    }
}
