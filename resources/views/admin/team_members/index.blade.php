@extends('admin.layout.master')

@section('title', 'Team Members')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Team Members</h1>
            <small class="text-muted">Manage admin users, their roles, and direct permissions.</small>
        </div>
        <a href="{{ route('admin.team-members.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>New Team Member
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Direct Permissions</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                            <tr>
                                <td class="fw-semibold">{{ $admin->name }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>
                                    @if($admin->role)
                                        <span class="badge bg-primary-subtle text-primary">{{ $admin->role->name }}</span>
                                    @else
                                        <span class="badge bg-light text-muted">No role</span>
                                    @endif
                                </td>
                                <td>
                                    @if($admin->permissions->isEmpty())
                                        <span class="badge bg-light text-muted">None</span>
                                    @else
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($admin->permissions as $permission)
                                                <span class="badge bg-light text-primary">{{ $permission->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.team-members.edit', $admin) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.team-members.destroy', $admin) }}" method="POST" onsubmit="return confirm('Delete this team member?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No team members found. Add one to get started.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
