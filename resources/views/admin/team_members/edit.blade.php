@extends('admin.layout.master')

@section('title', 'Edit Team Member')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Team Member</h1>
            <small class="text-muted">Update profile details, role assignment, and direct permissions.</small>
        </div>
        <a href="{{ route('admin.team-members.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Team Members
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.team-members.update', $admin) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $admin->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $admin->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-muted">(leave blank to keep current)</span></label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="role_id" class="form-label">Role <span class="text-muted">(optional)</span></label>
                            <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror">
                                <option value="">-- Select a role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ (string) old('role_id', $admin->role_id) === (string) $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                    <div class="col-lg-8">
                        <h5 class="mb-3">Direct Permissions</h5>
                        <p class="text-muted small">Select additional permissions beyond the role. Uncheck to remove direct access.</p>
                        <div class="row g-3">
                            @forelse($permissions as $permission)
                                <div class="col-md-6">
                                    <div class="form-check border rounded p-3 shadow-sm">
                                        <input class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission-{{ $permission->id }}" name="permissions[]" {{ in_array($permission->id, old('permissions', $admin->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission-{{ $permission->id }}">
                                            <strong>{{ $permission->name }}</strong>
                                            <div class="text-muted small">{{ $permission->slug }}</div>
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        No permissions available yet. Create permissions first to assign them here.
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
