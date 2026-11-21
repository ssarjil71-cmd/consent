@extends('admin.layout.master')

@section('title', 'Categories')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Categories</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Category
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Organization Type</th>
                            <th>Organization</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->organizationType->name ?? 'N/A' }}</td>
                                <td>{{ $category->organization ? $category->organization->name : 'Global' }}</td>
                                <td>{{ $category->createdByAdmin ? $category->createdByAdmin->name : 'Organization' }}</td>
                                <td>{{ $category->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route('admin.categories.edit', $category) }}" class="dropdown-item">
                                                    <i class="fas fa-edit me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                        <i class="fas fa-trash me-2"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 d-flex justify-content-center">
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
            <style>
                .pagination .page-link {
                    font-size: 0.875rem;
                    padding: 0.4rem 0.75rem;
                    min-width: 2rem;
                    color: #0f172a;
                }

                .pagination .page-item:first-child .page-link span[aria-hidden="true"],
                .pagination .page-item:last-child .page-link span[aria-hidden="true"] {
                    font-size: 1rem;
                    line-height: 1;
                }

                .pagination .page-link:hover {
                    background-color: #e9ecef;
                    color: #0f172a;
                }

                .pagination .page-item.active .page-link {
                    background-color: #08bc7dff;
                    border-color: #08bc7dff;
                    color: #fff;
                }
            </style>
        </div>
    </div>
</div>
@endsection
