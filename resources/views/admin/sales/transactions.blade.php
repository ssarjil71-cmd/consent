@extends('admin.layout.master')

@section('title', 'Sales - Transactions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1>Sales Transactions</h1>
                <p class="text-muted">All balance addition transactions and revenue records</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Transaction History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Transaction ID</th>
                                    <th>Organization</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $index => $transaction)
                                    <tr>
                                        <td>{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $index + 1 }}</td>
                                        <td>
                                            <code>{{ $transaction->transaction_id ?? 'TXN' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</code>
                                        </td>
                                        <td>
                                            @if($transaction->organization)
                                                <strong>{{ $transaction->organization->name }}</strong>
                                                <br><small class="text-muted">{{ $transaction->organization->email }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <strong class="text-success">₹{{ number_format($transaction->amount, 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($transaction->payment_method == 'razorpay')
                                                <span class="badge bg-info">Razorpay</span>
                                            @else
                                                <span class="badge bg-secondary">Balance</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Completed</span>
                                        </td>
                                        <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($transactions->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
