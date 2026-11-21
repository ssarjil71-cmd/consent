<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\RiskConsent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RiskConsentController extends Controller
{
    /**
     * Show a listing of risk consent entries.
     */
    public function index()
    {
        $organization = Auth::guard('organization')->user();

        abort_unless($organization, 403);

        $riskConsents = collect();

        try {
            if (class_exists(RiskConsent::class) && Schema::hasTable('risk_consents')) {
                $riskConsents = RiskConsent::query()
                    ->where('organization_id', $organization->id)
                    ->with('category')
                    ->latest()
                    ->get();
            }
        } catch (\Throwable $e) {
            $riskConsents = collect();
        }

        return view('organization.risk-consents.index', compact('organization', 'riskConsents'));
    }

    /**
     * Display the risk consent creation form.
     */
    public function create()
    {
        $organization = Auth::guard('organization')->user();

        abort_unless($organization, 403);

        $categories = $this->categoriesForOrganization($organization);

        return view('organization.risk-consents.create', compact('organization', 'categories'));
    }

    /**
     * Store a newly created risk consent.
     */
    public function store(Request $request)
    {
        $organization = Auth::guard('organization')->user();

        abort_unless($organization, 403);

        $validated = $this->validatePayload($request, $organization);

        $riskConsent = RiskConsent::query()->create($validated + [
            'organization_id' => $organization->id,
        ]);

        return redirect()
            ->route('org.risk-consents.edit', $riskConsent)
            ->with('success', 'Risk consent content saved successfully.');
    }

    /**
     * Show the edit form for a risk consent.
     */
    public function edit(RiskConsent $riskConsent)
    {
        $organization = Auth::guard('organization')->user();

        abort_unless($organization && $riskConsent->organization_id === $organization->id, 403);

        $categories = $this->categoriesForOrganization($organization);

        return view('organization.risk-consents.edit', compact('organization', 'riskConsent', 'categories'));
    }

    /**
     * Update an existing risk consent.
     */
    public function update(Request $request, RiskConsent $riskConsent)
    {
        $organization = Auth::guard('organization')->user();

        abort_unless($organization && $riskConsent->organization_id === $organization->id, 403);

        $validated = $this->validatePayload($request, $organization, $riskConsent->id);

        $riskConsent->update($validated);

        return back()->with('success', 'Risk consent content updated successfully.');
    }

    /**
     * Delete a risk consent entry.
     */
    public function destroy(RiskConsent $riskConsent)
    {
        $organization = Auth::guard('organization')->user();

        abort_unless($organization && $riskConsent->organization_id === $organization->id, 403);

        $riskConsent->delete();

        return redirect()
            ->route('org.risk-consents.index')
            ->with('success', 'Risk consent removed successfully.');
    }

    /**
     * Validate incoming request payload.
     */
    protected function validatePayload(Request $request, $organization, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content_en' => ['nullable', 'string'],
            'content_hi' => ['nullable', 'string'],
            'content_mr' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'is_active' => ['required', 'in:0,1'],
        ]);

        if (!empty($validated['category_id'])) {
            $categoryAllowed = Category::query()
                ->where('id', $validated['category_id'])
                ->where(function ($query) use ($organization) {
                    $query->where('organization_id', $organization->id)
                        ->orWhere(function ($sub) use ($organization) {
                            $sub->whereNull('organization_id')
                                ->where(function ($typeScoped) use ($organization) {
                                    $typeId = $organization?->type_id;
                                    $typeScoped->whereNull('organization_type_id');

                                    if ($typeId) {
                                        $typeScoped->orWhere('organization_type_id', $typeId);
                                    }
                                });
                        });
                })
                ->exists();

            abort_unless($categoryAllowed, 403);
        }

        $validated['is_active'] = (bool) ((int) $validated['is_active']);

        return $validated;
    }

    /**
     * Fetch available categories for the organization.
     */
    protected function categoriesForOrganization($organization)
    {
        return Category::query()
            ->where(function ($query) use ($organization) {
                $query->whereNull('organization_id')
                    ->where(function ($sub) use ($organization) {
                        $sub->whereNull('organization_type_id')
                            ->orWhere('organization_type_id', $organization->type_id);
                    });
            })
            ->orWhere(function ($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->orderBy('name')
            ->get();
    }
}
