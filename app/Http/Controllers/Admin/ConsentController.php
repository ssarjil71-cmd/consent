<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consent;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ConsentController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter');

        $query = Consent::with('organization');

        switch ($filter) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'approved':
            case 'pending':
            case 'rejected':
                $query->where('status', $filter);
                break;
            default:
                // no extra filter
                break;
        }

        $consents = $query->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.consents.index', compact('consents', 'filter'));
    }

    public function show(Consent $consent)
    {
        // Load consent with organization relationship
        $consent->load('organization');
        
        return view('admin.consents.show', compact('consent'));
    }
}
