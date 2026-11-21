@extends('admin.layout.master')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>All Organizations</h3>
    <a href="{{ route('admin.organizations.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Organization
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Status</th>
                        <!-- <th>QR</th> -->
                        <th>Transaction Amount</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organizations as $org)
                        <tr>
                            <td>{{ $org->id }}</td>
                            <td>{{ $org->name }}</td>
                            <td>{{ $org->email }}</td>
                            <td>{{ optional($org->type)->name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $org->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($org->status) }}
                                </span>
                            </td>
                            <!-- <td>
                                @php($loginUrl = route('org.login', $org->id))
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($loginUrl) }}" alt="QR" style="width:72px;height:72px">
                                <div><a href="{{ $loginUrl }}" target="_blank" class="small">Open</a></div>
                            </td> -->
                            <td>₹{{ number_format($org->transaction_amount, 2) }}</td>
                            <td>{{ $org->created_at->format('d M Y') }}</td>
                           <td>
    <a href="{{ route('admin.organizations.edit', $org) }}"
       class="btn btn-sm btn-warnning d-inline-flex align-items-center gap-1"
       title="Edit">

        <!-- SVG Edit Icon -->
        <svg xmlns="http://www.w3.org/2000/svg"
             width="14" height="14"
             fill="currentColor"
             viewBox="0 0 16 16">
            <path d="M15.502 1.94a.5.5 0 0 1 0 .706l-1.439 1.439-2.122-2.122
                     1.439-1.439a.5.5 0 0 1 .707 0l1.415 1.415z"/>
            <path d="M13.439 3.439 11.318 1.318 4.939 7.697a.5.5 0 0 0-.121.196
                     l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0
                     .196-.12l6.5-6.5z"/>
            <path fill-rule="evenodd"
                  d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0
                     1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1
                     -.5-.5v-11a.5.5 0 0 1 .5-.5h6a.5.5 0 0 0 0-1h-6A1.5
                     1.5 0 0 0 1 2.5v11z"/>
        </svg>

        <span>Edit</span>
    </a>
</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No organizations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
