<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrganizationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $organizationId = $request->query('organization_id');
        
        $query = Category::with(['organizationType', 'organization', 'createdByAdmin']);
        
        if ($organizationId) {
            // Show categories for a specific organization
            $query->where('organization_id', $organizationId);
        }
        
        $categories = $query->latest()->paginate(20);
        
        // Get all organizations for the filter dropdown
        $organizations = \App\Models\Organization::pluck('name', 'id');

        return view('admin.categories.index', compact('categories', 'organizations', 'organizationId'));
    }

    public function create()
    {
        $organizationTypes = OrganizationType::pluck('name', 'id');
        return view('admin.categories.create', compact('organizationTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization_type_id' => 'required|exists:organization_types,id',
        ]);

        // Create a global category (not tied to a specific organization)
        Category::create([
            'name' => $validated['name'],
            'organization_type_id' => $validated['organization_type_id'],
            'created_by_admin' => Auth::id(),
            // organization_id will be null for global categories
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $organizationTypes = OrganizationType::pluck('name', 'id');
        return view('admin.categories.edit', compact('category', 'organizationTypes'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization_type_id' => 'required|exists:organization_types,id',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
