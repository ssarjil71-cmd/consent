@extends('organization.layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-check-circle mr-2"></i>
                        Account Activated Successfully!
                    </h4>
                </div>
                <div class="card-body p-4 text-center">
                    <!-- Success Icon -->
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>

                    <!-- Success Message -->
                    <h3 class="text-success mb-3">Welcome to {{ $organization->name }}!</h3>
                    <p class="text-muted mb-4">
                        Your account has been successfully activated. You now have full access to all features and services.
                    </p>

                    <!-- Payment Confirmation -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-receipt mr-2"></i>
                                Payment Confirmation
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Activation Fee Paid:</strong></p>
                                    <p class="text-success h5">₹{{ number_format($organization->paid_amount, 2) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Payment Date:</strong></p>
                                    <p class="text-muted">{{ $organization->payment_date ? $organization->payment_date->format('d M Y, h:i A') : 'N/A' }}</p>
                                </div>
                            </div>
                            @if($organization->payment_id)
                            <div class="mt-2">
                                <p class="mb-1"><strong>Transaction ID:</strong></p>
                                <p class="text-muted small">{{ $organization->payment_id }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Account Status -->
                    <div class="alert alert-success mb-4">
                        <h5 class="alert-heading">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Account Status: Active
                        </h5>
                        <p class="mb-0">
                            Your organization account is now active and ready to use. You can start creating consents, managing patients, and accessing all platform features.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('org.dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Go to Dashboard
                        </a>
                        <a href="{{ route('org.profile') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-user mr-2"></i>
                            View Profile
                        </a>
                    </div>

                    <!-- Next Steps -->
                    <div class="mt-4">
                        <h6 class="text-muted mb-3">What's Next?</h6>
                        <div class="row text-left">
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users text-primary mb-2" style="font-size: 2rem;"></i>
                                        <h6 class="card-title">Add Staff</h6>
                                        <p class="card-text small text-muted">Add team members who can manage consents</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-folder-plus text-success mb-2" style="font-size: 2rem;"></i>
                                        <h6 class="card-title">Create Categories</h6>
                                        <p class="card-text small text-muted">Organize consents by categories</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-file-contract text-warning mb-2" style="font-size: 2rem;"></i>
                                        <h6 class="card-title">Start Creating</h6>
                                        <p class="card-text small text-muted">Create your first consent form</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
