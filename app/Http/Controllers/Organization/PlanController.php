<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organization');
    }

    public function index()
    {
        $organization = Auth::guard('organization')->user();
        $plans = $organization->plans()->latest()->get();
        
        return view('organization.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('organization.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'is_active' => 'boolean',
            'max_consents' => 'nullable|integer|min:1',
            'per_consent_amount' => 'required|numeric|min:0',
        ]);

        $organization = Auth::guard('organization')->user();
        
        $plan = $organization->plans()->create([
            'name' => $request->name,
            'description' => $request->description,
            'amount' => $request->amount,
            'duration_months' => $request->duration_months,
            'features' => $request->features,
            'is_active' => $request->boolean('is_active', true),
            'max_consents' => $request->max_consents,
            'per_consent_amount' => $request->per_consent_amount,
        ]);

        return redirect()
            ->route('org.plans.index')
            ->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        $this->authorizePlanAccess($plan);
        
        return view('organization.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $this->authorizePlanAccess($plan);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'is_active' => 'boolean',
            'max_consents' => 'nullable|integer|min:1',
            'per_consent_amount' => 'required|numeric|min:0',
        ]);

        $plan->update([
            'name' => $request->name,
            'description' => $request->description,
            'amount' => $request->amount,
            'duration_months' => $request->duration_months,
            'features' => $request->features,
            'is_active' => $request->boolean('is_active', true),
            'max_consents' => $request->max_consents,
            'per_consent_amount' => $request->per_consent_amount,
        ]);

        return redirect()
            ->route('org.plans.index')
            ->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        $this->authorizePlanAccess($plan);
        
        $plan->delete();

        return redirect()
            ->route('org.plans.index')
            ->with('success', 'Plan deleted successfully.');
    }

    private function authorizePlanAccess(Plan $plan)
    {
        $organization = Auth::guard('organization')->user();
        
        if ($plan->organization_id !== $organization->id) {
            abort(403, 'Unauthorized access to this plan.');
        }
    }
}
