<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\Msg91DLTService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class OrganizationController extends Controller
{
    protected $msg91Service;

    public function __construct(Msg91DLTService $msg91Service)
    {
        $this->msg91Service = $msg91Service;
    }

    public function categoriesByType(Request $request)
    {
        $request->validate([
            'type_id' => 'required|exists:organization_types,id'
        ]);

        $categories = \App\Models\Category::where('organization_type_id', $request->type_id)
            ->whereNull('organization_id') // Only global categories for new organizations
            ->select('id', 'name', 'organization_id')
            ->get();

        return response()->json($categories);
    }

    public function index()
    {
        $organizations = Organization::with('type')->get();
        return view('admin.organizations.index', compact('organizations'));
    }

    public function create()
    {
        $types = OrganizationType::all();
        $allCategories = \App\Models\Category::whereNull('organization_id') // Only global categories
            ->get();
            
        return view('admin.organizations.create', compact('types', 'allCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_id' => 'required|exists:organization_types,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:organizations',
            'mobile' => 'nullable|string|regex:/^[6-9]\d{9}$/',
            'password' => 'required|string|min:8',
            'status' => 'required|in:active,inactive',
            'activation_fee' => 'required|numeric|min:0',
            'transaction_amount' => 'nullable|numeric|min:0',
            'per_consent_amount' => 'nullable|numeric|min:0',
            'consent_label' => 'nullable|string|max:50',
            'terms_text' => 'nullable|string',
            'terms_text_hi' => 'nullable|string',
            'terms_text_mr' => 'nullable|string',
            'liability_release_text' => 'nullable|string',
            'liability_release_text_hi' => 'nullable|string',
            'liability_release_text_mr' => 'nullable|string',
            'risk_acknowledgement_text' => 'nullable|string',
            'risk_acknowledgement_text_hi' => 'nullable|string',
            'risk_acknowledgement_text_mr' => 'nullable|string',
        ]);

        $startingBalance = $validated['transaction_amount'] ?? 0;

        $organization = Organization::create([
            'type_id' => $validated['type_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
            'activation_fee' => $validated['activation_fee'],
            'transaction_amount' => $startingBalance ?? 0,
            'per_consent_amount' => $validated['per_consent_amount'] ?? 0,
            'consent_label' => $validated['consent_label'] ?? null,
            'terms_text' => $validated['terms_text'] ?? null,
            'terms_text_hi' => $validated['terms_text_hi'] ?? null,
            'terms_text_mr' => $validated['terms_text_mr'] ?? null,
            'liability_release_text' => $validated['liability_release_text'] ?? null,
            'liability_release_text_hi' => $validated['liability_release_text_hi'] ?? null,
            'liability_release_text_mr' => $validated['liability_release_text_mr'] ?? null,
            'risk_acknowledgement_text' => $validated['risk_acknowledgement_text'] ?? null,
            'risk_acknowledgement_text_hi' => $validated['risk_acknowledgement_text_hi'] ?? null,
            'risk_acknowledgement_text_mr' => $validated['risk_acknowledgement_text_mr'] ?? null,
        ]);

        // Record initial balance as a transaction so it appears in the ledger
        if (Schema::hasTable('transactions') && $startingBalance != 0) {
            Transaction::create([
                'organization_id' => $organization->id,
                'consent_id' => null,
                'amount' => $startingBalance,
                'type' => $startingBalance > 0 ? 'admin_initial_topup' : 'admin_initial_adjustment',
                'description' => 'Initial balance set by admin',
                'balance_after' => $startingBalance,
            ]);
        }

        // Send welcome message if mobile number is provided
        if (!empty($validated['mobile'])) {
            $loginLink = route('org.login');
            $this->msg91Service->sendWelcomeMessage(
                $validated['mobile'], 
                $validated['email'], 
                $validated['password'], 
                $loginLink
            );
        }

        return redirect()->route('admin.organizations.index')
                         ->with('success', 'Organization added successfully!');
    }

    public function edit(Organization $organization)
    {
        $types = OrganizationType::all();
        $allCategories = \App\Models\Category::where('organization_type_id', $organization->type_id)
            ->whereNull('organization_id') // Only global categories
            ->orWhere('organization_id', $organization->id) // Or categories specific to this org
            ->get();
            
        $assignedCategoryIds = $organization->categories->pluck('id')->toArray();
        
        return view('admin.organizations.edit', compact(
            'organization', 
            'types', 
            'allCategories',
            'assignedCategoryIds'
        ));
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'type_id' => 'required|exists:organization_types,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:organizations,email,' . $organization->id,
            'mobile' => 'nullable|string|regex:/^[6-9]\d{9}$/',
            'password' => 'nullable|string|min:8',
            'status' => 'required|in:active,inactive',
            'activation_fee' => 'required|numeric|min:0',
            'transaction_amount' => 'nullable|numeric|min:0',
            'per_consent_amount' => 'nullable|numeric|min:0',
            'consent_label' => 'nullable|string|max:50',
            'terms_text' => 'nullable|string',
            'terms_text_hi' => 'nullable|string',
            'terms_text_mr' => 'nullable|string',
            'liability_release_text' => 'nullable|string',
            'liability_release_text_hi' => 'nullable|string',
            'liability_release_text_mr' => 'nullable|string',
            'risk_acknowledgement_text' => 'nullable|string',
            'risk_acknowledgement_text_hi' => 'nullable|string',
            'risk_acknowledgement_text_mr' => 'nullable|string',
        ]);

        $oldBalance = (float) ($organization->transaction_amount ?? 0);
        $newBalance = isset($validated['transaction_amount']) ? (float) $validated['transaction_amount'] : 0;

        $updateData = [
            'type_id' => $validated['type_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'status' => $validated['status'],
            'activation_fee' => $validated['activation_fee'],
            'transaction_amount' => $newBalance,
            'per_consent_amount' => $validated['per_consent_amount'] ?? 0,
            'consent_label' => $validated['consent_label'] ?? null,
            'terms_text' => $validated['terms_text'] ?? null,
            'terms_text_hi' => $validated['terms_text_hi'] ?? null,
            'terms_text_mr' => $validated['terms_text_mr'] ?? null,
            'liability_release_text' => $validated['liability_release_text'] ?? null,
            'liability_release_text_hi' => $validated['liability_release_text_hi'] ?? null,
            'liability_release_text_mr' => $validated['liability_release_text_mr'] ?? null,
            'risk_acknowledgement_text' => $validated['risk_acknowledgement_text'] ?? null,
            'risk_acknowledgement_text_hi' => $validated['risk_acknowledgement_text_hi'] ?? null,
            'risk_acknowledgement_text_mr' => $validated['risk_acknowledgement_text_mr'] ?? null,
        ];

        // Update password only if provided
        if ($validated['password'] ?? false) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $organization->update($updateData);

        // Record admin balance change as a transaction so it appears in the ledger
        $diff = $newBalance - $oldBalance;
        if (Schema::hasTable('transactions') && $diff != 0) {
            Transaction::create([
                'organization_id' => $organization->id,
                'consent_id' => null,
                'amount' => $diff,
                'type' => $diff > 0 ? 'admin_topup' : 'admin_deduction',
                'description' => 'Admin updated balance from ₹' . number_format($oldBalance, 2) . ' to ₹' . number_format($newBalance, 2),
                'balance_after' => $newBalance,
            ]);
        }
        
        // Sync categories if provided
        if ($request->has('categories')) {
            // Convert comma-separated string to array of integers
            $categoryIds = !empty($request->categories[0]) 
                ? array_map('intval', explode(',', $request->categories[0]))
                : [];
            
            $organization->categories()->sync($categoryIds);
        } else {
            $organization->categories()->detach();
        }

        // Send welcome message if mobile number is provided and either mobile or password changed
        if (!empty($validated['mobile'])) {
            $mobileChanged = $organization->wasChanged('mobile');
            $passwordChanged = isset($updateData['password']);
            
            if ($mobileChanged || $passwordChanged) {
                $loginLink = route('org.login');
                $this->msg91Service->sendWelcomeMessage(
                    $validated['mobile'], 
                    $validated['email'], 
                    $passwordChanged ? $validated['password'] : '[unchanged]', 
                    $loginLink
                );
            }
        }

        return redirect()->route('admin.organizations.index')
                         ->with('success', 'Organization updated successfully!');
    }
}
