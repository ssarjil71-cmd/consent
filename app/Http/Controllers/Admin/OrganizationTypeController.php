<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationType;
use Illuminate\Http\Request;

class OrganizationTypeController extends Controller
{
    public function index()
    {
        $types = OrganizationType::all();
        return view('admin.organization_types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.organization_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:organization_types',
            'description' => 'nullable|string',
            'terms_text' => 'nullable|string',
            'liability_release_text' => 'nullable|string',
            'risk_acknowledgement_text' => 'nullable|string',
        ]);

        OrganizationType::create($request->all());
        
        return redirect()
            ->route('admin.organization_types.index')
            ->with('success', 'Organization type created successfully!');
    }

    public function edit(OrganizationType $organizationType)
    {
        return view('admin.organization_types.edit', compact('organizationType'));
    }

    public function update(Request $request, OrganizationType $organizationType)
    {
        $request->validate([
            'name' => 'required|unique:organization_types,name,' . $organizationType->id,
            'description' => 'nullable|string',
            'terms_text' => 'nullable|string',
            'liability_release_text' => 'nullable|string',
            'risk_acknowledgement_text' => 'nullable|string',
        ]);

        $organizationType->update($request->all());
        
        return redirect()
            ->route('admin.organization_types.index')
            ->with('success', 'Organization type updated successfully!');
    }
}
