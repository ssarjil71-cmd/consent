@extends('organization.layouts.app')

@section('content')
<!-- Full Screen Activation Modal -->
<div class="activation-modal-overlay" id="activationModal">
    <div class="activation-modal-content">
        <!-- Modal Header -->
        <div class="activation-modal-header">
            <div class="text-center text-white">
                <i class="fas fa-exclamation-triangle mb-3" style="font-size: 3rem;"></i>
                <h3>Account Activation Required</h3>
                <p class="mb-0">Welcome to {{ $organization->name }}!</p>
            </div>
        </div>
        
        <!-- Modal Body -->
        <div class="activation-modal-body">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <!-- Alert Message -->
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading">
                                <i class="fas fa-info-circle mr-2"></i>
                                Activate Your Account
                            </h5>
                            <p class="mb-0">
                                Your organization account has been created successfully. To activate your account and access all features, please pay the one-time activation fee.
                            </p>
                        </div>

                        <!-- Payment Details Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-rupee-sign mr-2"></i>
                                    Activation Fee Details
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Organization:</strong></p>
                                        <p class="text-muted">{{ $organization->name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Activation Fee:</strong></p>
                                        <p class="text-primary h4">₹{{ number_format($organization->activation_fee, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <form id="paymentForm" action="{{ route('org.activation.payment') }}" method="POST">
                            @csrf
                            
                            <!-- Payment Method Selection -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Select Payment Method:</label>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" 
                                                   id="razorpay_payment" value="razorpay" checked required>
                                            <label class="form-check-label" for="razorpay_payment">
                                                <i class="fas fa-credit-card mr-2"></i>
                                                Pay Online (Razorpay)
                                                <span class="badge badge-success ml-2">Recommended</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Fields -->
                            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">

                            <!-- Pay Button -->
                            <div class="text-center">
                                <button type="button" id="payButton" class="btn btn-success btn-lg px-5">
                                    <i class="fas fa-lock mr-2"></i>
                                    Pay ₹{{ number_format($organization->activation_fee, 2) }} Now
                                </button>
                            </div>
                        </form>

                        <!-- Help Section -->
                        <div class="mt-4 text-center">
                            <small class="text-muted">
                                <i class="fas fa-question-circle mr-1"></i>
                                Need help? Contact our support team for assistance.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Full Screen Modal -->
<style>
.activation-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.activation-modal-content {
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: modalSlideIn 0.3s ease-out;
}

.activation-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
    text-align: center;
}

.activation-modal-body {
    padding: 2rem;
    max-height: calc(90vh - 200px);
    overflow-y: auto;
}

/* Hide main content when modal is active */
body:has(.activation-modal-overlay) .main-sidebar,
body:has(.activation-modal-overlay) .main-header,
body:has(.activation-modal-overlay) .content-wrapper {
    filter: blur(5px);
    pointer-events: none;
    user-select: none;
}

/* Animation */
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Custom scrollbar */
.activation-modal-body::-webkit-scrollbar {
    width: 8px;
}

.activation-modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.activation-modal-body::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

.activation-modal-body::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}
</style>

<!-- Razorpay Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payButton = document.getElementById('payButton');
    const paymentForm = document.getElementById('paymentForm');
    
    payButton.addEventListener('click', function() {
        // Disable button to prevent multiple clicks
        payButton.disabled = true;
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        
        // Initialize Razorpay
        const options = {
            key: '{{ config("services.razorpay.key") }}',
            amount: {{ $organization->activation_fee * 100 }}, // Amount in paise
            currency: 'INR',
            name: '{{ $organization->name }}',
            description: 'Account Activation Fee',
            image: '{{ asset("images/logo.png") }}',
            prefill: {
                name: '{{ $organization->name }}',
                email: '{{ $organization->email }}',
                contact: '{{ $organization->mobile ?? "" }}'
            },
            theme: {
                color: '#667eea'
            },
            modal: {
                ondismiss: function() {
                    // Re-enable button if modal is closed
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="fas fa-lock mr-2"></i>Pay ₹{{ number_format($organization->activation_fee, 2) }} Now';
                }
            },
            handler: function(response) {
                // Payment successful
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                paymentForm.submit();
            }
        };
        
        const rzp = new Razorpay(options);
        rzp.open();
    });
});
</script>
@endsection
