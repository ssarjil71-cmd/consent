@extends('admin.layout.master')

@section('title', 'Sales - Organizations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1>Organizations - Sales Overview</h1>
                <p class="text-muted">Organizations ranked by revenue contribution</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Organizations by Revenue</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Organization Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Total Revenue</th>
                                    <th>Transactions</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($organizations as $index => $organization)
                                    <tr>
                                        <td>{{ ($organizations->currentPage() - 1) * $organizations->perPage() + $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $organization->name }}</strong>
                                            @if($organization->organization_type)
                                                <br><small class="text-muted">{{ $organization->organization_type->name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $organization->email }}</td>
                                        <td>{{ $organization->phone ?? 'N/A' }}</td>
                                        <td>
                                            @if($organization->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <strong>₹{{ number_format($organization->transactions_sum_amount ?? 0, 2) }}</strong>
                                        </td>
                                        <td class="text-center">{{ $organization->transactions_count ?? 0 }}</td>
                                        <td>{{ $organization->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($organizations->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $organizations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
