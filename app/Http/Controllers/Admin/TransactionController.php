<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Organization;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter');
        $organizationId = $request->query('organization');

        $query = Transaction::with('organization');

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        if ($filter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        }

        $transactions = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $organizations = Organization::orderBy('name')->get(['id', 'name']);

        return view('admin.transactions.index', compact('transactions', 'filter', 'organizationId', 'organizations'));
    }
}
