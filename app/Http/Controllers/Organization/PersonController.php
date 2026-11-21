<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PersonController extends Controller
{
    public function index()
    {
        $organization = Auth::guard('organization')->user();

        $persons = Person::where('organization_id', $organization->id)
            ->orderBy('name')
            ->paginate(12);

        return view('organization.persons.index', compact('organization', 'persons'));
    }

    public function create(Request $request)
    {
        $organization = Auth::guard('organization')->user();
        $consentId = $request->query('consent_id');
        return view('organization.persons.create', compact('organization', 'consentId'));
    }

    public function store(Request $request)
    {
        $organization = Auth::guard('organization')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'occupation' => 'required|string|max:100',
            'consent_id' => 'nullable|integer',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $signaturePath = null;
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('signatures/persons', 'public');
        }

        $person = Person::create([
            'organization_id' => $organization->id,
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'occupation' => $validated['occupation'],
            'signature_path' => $signaturePath,
        ]);

        // Redirect back to consent detail if provided, otherwise to dashboard persons create again or consents list
        if (!empty($validated['consent_id'])) {
            return redirect()->route('org.consents.show', $validated['consent_id'])
                ->with('success', 'Person added successfully. You can now assign them to the consent.');
        }

        return redirect()->route('org.dashboard')->with('success', 'Person added successfully.');
    }

    public function edit(Person $person)
    {
        $organization = Auth::guard('organization')->user();
        
        // Ensure the person belongs to the current organization
        if ($person->organization_id !== $organization->id) {
            abort(403);
        }

        return view('organization.persons.edit', compact('organization', 'person'));
    }

    public function update(Request $request, Person $person)
    {
        $organization = Auth::guard('organization')->user();
        
        // Ensure the person belongs to the current organization
        if ($person->organization_id !== $organization->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'occupation' => 'required|string|max:100',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $signaturePath = $person->signature_path;
        if ($request->hasFile('signature')) {
            // Delete old signature if exists
            if ($person->signature_path) {
                Storage::disk('public')->delete($person->signature_path);
            }
            $signaturePath = $request->file('signature')->store('signatures/persons', 'public');
        }

        $person->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'occupation' => $validated['occupation'],
            'signature_path' => $signaturePath,
        ]);

        return redirect()->route('org.persons.index')->with('success', 'Person updated successfully.');
    }

    public function destroy(Person $person)
    {
        $organization = Auth::guard('organization')->user();
        
        // Ensure the person belongs to the current organization
        if ($person->organization_id !== $organization->id) {
            abort(403);
        }

        // Delete signature if exists
        if ($person->signature_path) {
            Storage::disk('public')->delete($person->signature_path);
        }

        $person->delete();

        return redirect()->route('org.persons.index')->with('success', 'Person deleted successfully.');
    }
}
