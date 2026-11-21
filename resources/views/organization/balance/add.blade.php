@extends('organization.layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">Add Balance</h1>
        <p class="mt-2 text-gray-600">Add funds to your account to process consents seamlessly</p>
    </div>

    <!-- Current Balance Card -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-indigo-100 text-sm font-medium">Current Balance</p>
                <p class="text-4xl font-bold mt-2">₹{{ number_format($organization->transaction_amount, 2) }}</p>
            </div>
            <div class="bg-white/20 rounded-full p-3">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Add Balance Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Add Funds</h2>
        
        <div class="space-y-6">
            <!-- Amount Input -->
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Amount (₹)
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-lg">₹</span>
                    <input 
                        type="number" 
                        id="amount" 
                        name="amount" 
                        class="pl-8 pr-3 py-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg"
                        placeholder="Enter amount"
                        min="1"
                        max="100000"
                        step="0.01"
                        required
                    >
                </div>
                <p class="mt-2 text-sm text-gray-500">Enter the amount you want to add to your balance</p>
            </div>

            <!-- Quick Amount Buttons -->
            <div>
                <p class="text-sm font-medium text-gray-700 mb-3">Quick Amount</p>
                <div class="grid grid-cols-4 gap-3">
                    <button type="button" class="quick-amount px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 font-medium transition-colors" data-amount="500">₹500</button>
                    <button type="button" class="quick-amount px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 font-medium transition-colors" data-amount="1000">₹1,000</button>
                    <button type="button" class="quick-amount px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 font-medium transition-colors" data-amount="2000">₹2,000</button>
                    <button type="button" class="quick-amount px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 font-medium transition-colors" data-amount="5000">₹5,000</button>
                </div>
            </div>

            <!-- Pay with Razorpay Button -->
            <button 
                type="button" 
                id="payWithRazorpay" 
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Pay with Razorpay
            </button>
        </div>
    </div>
</div>

<!-- Razorpay Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const payButton = document.getElementById('payWithRazorpay');
    let razorpayOrder = null;

    // Quick amount buttons
    document.querySelectorAll('.quick-amount').forEach(button => {
        button.addEventListener('click', function() {
            amountInput.value = this.dataset.amount;
        });
    });

    // Pay with Razorpay
    payButton.addEventListener('click', async function() {
        const amount = parseFloat(amountInput.value);
        
        if (!amount || amount < 1) {
            alert('Please enter a valid amount');
            return;
        }

        payButton.disabled = true;
        payButton.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';

        try {
            // Create Razorpay order
            const response = await fetch('{{ route("org.balance.create-order") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ amount: amount })
            });

            // Check if response is ok
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Server response:', errorText);
                throw new Error(`Server error: ${response.status} - ${response.statusText}`);
            }

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            razorpayOrder = data;

            // Open Razorpay checkout
            const options = {
                key: data.razorpay_key,
                amount: data.amount * 100, // Convert to paise
                currency: 'INR',
                name: 'ConsentRX',
                description: 'Balance Addition',
                order_id: data.order_id,
                handler: function (response) {
                    // Payment successful
                    verifyPayment(response);
                },
                prefill: {
                    name: '{{ $organization->name }}',
                    email: '{{ $organization->email }}'
                },
                theme: {
                    color: '#4F46E5'
                },
                modal: {
                    ondismiss: function() {
                        payButton.disabled = false;
                        payButton.innerHTML = 'Pay with Razorpay';
                    },
                    escape: false,
                    handleback: false,
                    confirm_close: true,
                    animation: 'slideFromBottom'
                },
                notes: {
                    organization_id: '{{ $organization->id }}',
                    type: 'balance_addition'
                }
            };

            const rzp = new Razorpay(options);
            rzp.open();

        } catch (error) {
            alert('Error: ' + error.message);
            payButton.disabled = false;
            payButton.innerHTML = 'Pay with Razorpay';
        }
    });

    // Verify payment with server
    async function verifyPayment(response) {
        try {
            const verifyResponse = await fetch('{{ route("org.balance.confirm") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_signature: response.razorpay_signature
                })
            });

            const data = await verifyResponse.json();

            if (data.success) {
                alert('Payment successful! Balance added: ₹' + data.amount + '\nNew Balance: ₹' + data.new_balance);
                window.location.reload();
            } else {
                alert('Payment verification failed: ' + data.error);
                payButton.disabled = false;
                payButton.innerHTML = 'Pay with Razorpay';
            }
        } catch (error) {
            alert('Error verifying payment: ' + error.message);
            payButton.disabled = false;
            payButton.innerHTML = 'Pay with Razorpay';
        }
    }
});
</script>
@endsection
