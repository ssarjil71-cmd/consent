<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Organization;
use App\Models\Transaction;
use App\Models\Consent;

class SalesController extends Controller
{
    public function dashboard()
    {
        // Get sales metrics
        $totalRevenue = Transaction::where('type', 'balance_add')
            ->sum('amount');

        $monthlyRevenue = Transaction::where('type', 'balance_add')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $totalOrganizations = Organization::count();
        $activeOrganizations = Organization::where('status', 'active')->count();

        // Get recent transactions
        $recentTransactions = Transaction::with('organization')
            ->where('type', 'balance_add')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get monthly revenue data for chart (last 6 months)
        $monthlyRevenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Transaction::where('type', 'balance_add')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('amount');
            
            $monthlyRevenueData[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Get top organizations by revenue
        $topOrganizations = Organization::withSum(['transactions' => function($query) {
                $query->where('type', 'balance_add');
            }], 'amount')
            ->orderByDesc('transactions_sum_amount')
            ->take(5)
            ->get();

        // Get consent statistics
        $totalConsents = Consent::count();
        $approvedConsents = Consent::where('status', 'approved')->count();
        $pendingConsents = Consent::where('status', 'pending')->count();

        return view('admin.sales.dashboard', compact(
            'totalRevenue',
            'monthlyRevenue',
            'totalOrganizations',
            'activeOrganizations',
            'recentTransactions',
            'monthlyRevenueData',
            'topOrganizations',
            'totalConsents',
            'approvedConsents',
            'pendingConsents'
        ));
    }

    public function organizations()
    {
        $organizations = Organization::with(['transactions' => function($query) {
                $query->where('type', 'balance_add');
            }])
            ->withSum(['transactions' => function($query) {
                $query->where('type', 'balance_add');
            }], 'amount')
            ->withCount(['transactions' => function($query) {
                $query->where('type', 'balance_add');
            }])
            ->orderByDesc('transactions_sum_amount')
            ->paginate(20);

        return view('admin.sales.organizations', compact('organizations'));
    }

    public function transactions()
    {
        $transactions = Transaction::with('organization')
            ->where('type', 'balance_add')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.sales.transactions', compact('transactions'));
    }
}
