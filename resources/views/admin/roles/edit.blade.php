@extends('admin.layout.master')

@section('title', 'Edit Role')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Role</h1>
            <small class="text-muted">Update role details and adjust the permissions it grants.</small>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Roles
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-muted">(optional)</span></label>
                            <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $role->slug) }}" placeholder="e.g. content-manager">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <h5 class="mb-3">Assign Permissions</h5>
                        <div class="row g-3">
                            @forelse($permissions as $permission)
                                <div class="col-md-6">
                                    <div class="form-check border rounded p-3 shadow-sm">
                                        <input class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission-{{ $permission->id }}" name="permissions[]" {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission-{{ $permission->id }}">
                                            <strong>{{ $permission->name }}</strong>
                                            <div class="text-muted small">{{ $permission->slug }}</div>
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        No permissions available yet. Add permissions first, then assign them to this role.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        @error('permissions')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
