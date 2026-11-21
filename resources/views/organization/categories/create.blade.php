@extends('organization.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Add New Category</h1>
        <a href="{{ route('org.categories.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <i class="fas fa-arrow-left mr-1"></i> Back to Categories
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('org.categories.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        This category will be available only for your organization.
                    </p>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        Organization Type: <span class="font-medium">{{ $organization->type->name ?? 'N/A' }}</span>
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('org.categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center">
                        <i class="fas fa-save mr-1"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
