<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $organization = auth('organization')->user()->load('type');
        
        // Debug: Log organization details
        \Log::info('Organization Details:', [
            'id' => $organization->id,
            'name' => $organization->name,
            'type_id' => $organization->type_id,
            'type' => $organization->type
        ]);

        // Get both global categories (created by admin for this organization type)
        // and categories specific to this organization
        $query = Category::with(['organizationType', 'createdByAdmin'])
            ->where(function($query) use ($organization) {
                $query->where('organization_type_id', $organization->type_id)
                      ->where(function($q) use ($organization) {
                          $q->whereNull('organization_id') // Global categories
                            ->orWhere('organization_id', $organization->id); // Organization's own categories
                      });
            });

        // Debug: Log the SQL query
        \Log::info('Categories Query:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
        
        $categories = $query->latest()->paginate(20);

        // Debug: Log the results
        \Log::info('Categories Found:', ['count' => $categories->count()]);

        return view('organization.categories.index', compact('organization', 'categories'));
    }

    public function create()
    {
        $organization = auth('organization')->user();
        return view('organization.categories.create', compact('organization'));
    }

    public function store(Request $request)
    {
        try {
            $organization = auth('organization')->user();
            
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    // Make sure the name is unique for this organization
                    function ($attribute, $value, $fail) use ($organization) {
                        $exists = \App\Models\Category::where('name', $value)
                            ->where('organization_id', $organization->id)
                            ->exists();
                        if ($exists) {
                            $fail('The category name has already been taken.');
                        }
                    },
                ],
            ]);

            // Create organization-specific category
            $category = new \App\Models\Category([
                'name' => $validated['name'],
                'organization_type_id' => $organization->type_id,
                'organization_id' => $organization->id,
            ]);
            
            $organization->categories()->save($category);

            return redirect()->route('org.categories.index')
                ->with('success', 'Category created successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Error creating category: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating category. Please try again.');
        }
    }
}
