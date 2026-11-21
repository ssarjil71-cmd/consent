@extends('admin.layout.master')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3>Transactions</h3>
        @if($filter === 'today')
            <small class="text-muted">Today's transactions</small>
        @else
            <small class="text-muted">All transactions</small>
        @endif
    </div>
    <form method="GET" class="d-flex align-items-center gap-2">
        @if($filter)
            <input type="hidden" name="filter" value="{{ $filter }}">
        @endif
        <label class="me-2 mb-0 small text-muted">Organization:</label>
        <select name="organization" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All</option>
            @foreach($organizations as $org)
                <option value="{{ $org->id }}" {{ ($organizationId ?? null) == $org->id ? 'selected' : '' }}>
                    {{ $org->name }}
                </option>
            @endforeach
        </select>
    </form>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Organization</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Balance After</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td>{{ $tx->id }}</td>
                            <td>{{ optional($tx->organization)->name ?? '—' }}</td>
                            <td>₹ {{ number_format($tx->amount, 2) }}</td>
                            <td><span class="badge bg-secondary">{{ $tx->type }}</span></td>
                            <td>{{ $tx->description }}</td>
                            <td>₹ {{ number_format($tx->balance_after, 2) }}</td>
                            <td>{{ $tx->created_at?->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3">
            {{ $transactions->links('pagination::bootstrap-4') }}
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
