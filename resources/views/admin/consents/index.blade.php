@extends('admin.layout.master')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3>Consents</h3>
        @if($filter)
            <small class="text-muted">Filter: {{ ucfirst($filter) }}</small>
        @else
            <small class="text-muted">All consents</small>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Organization</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consents as $consent)
                        <tr>
                            <td>{{ $consent->id }}</td>
                            <td>{{ optional($consent->organization)->name ?? '—' }}</td>
                            <td><a href="{{ route('admin.consents.show', $consent->id) }}" class="text-decoration-none">{{ $consent->name }}</a></td>
                            <td>{{ $consent->email ?: '—' }}</td>
                            <td>{{ $consent->phone ?: '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $consent->status === 'approved' ? 'success' : ($consent->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($consent->status ?? 'pending') }}
                                </span>
                            </td>
                            <td>{{ $consent->created_at?->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No consents found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3">
            {{ $consents->links('pagination::bootstrap-4') }}
        </div>
        <style>
            .pagination .page-link {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
                border: 1px solid #dee2e6;
                color: #000;
            }
            .pagination .page-link:hover {
                background-color: #e9ecef;
                color: #000;
            }
            .pagination .page-item.active .page-link {
                background-color: #08bc7dff;
                border-color: #08bc7dff;
                color: #fff;
            }
        </style>
    </div>
</div>
@endsection
