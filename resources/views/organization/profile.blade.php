@extends('organization.layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <!-- Header Section -->
  <div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Organization Profile</h1>
    <p class="mt-2 text-sm text-gray-600">Manage your organization's details and settings</p>
  </div>

  <!-- Update Profile Information -->
  <div class="bg-white overflow-hidden shadow rounded-lg mb-8">
    <div class="px-6 py-5 border-b border-gray-200">
      <h3 class="text-lg font-medium leading-6 text-gray-900">Update Profile Information</h3>
      <p class="text-sm text-gray-500 mt-1">Update your organization name and email address</p>
    </div>
    <div class="px-6 py-6">
      <form action="{{ route('org.profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Organization Name</label>
            <input type="text" 
                   name="name" 
                   id="name" 
                   value="{{ $organization->name }}"
                   class="focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2 sm:text-sm border-gray-300 rounded-lg" 
                   required>
          </div>
          
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   value="{{ $organization->email }}"
                   class="focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2 sm:text-sm border-gray-300 rounded-lg" 
                   required>
          </div>
        </div>
        
        <div class="flex justify-end">
          <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
            </svg>
            Update Profile
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Update Password -->
  <div class="bg-white overflow-hidden shadow rounded-lg mb-8">
    <div class="px-6 py-5 border-b border-gray-200">
      <h3 class="text-lg font-medium leading-6 text-gray-900">Update Password</h3>
      <p class="text-sm text-gray-500 mt-1">Change your account password</p>
    </div>
    <div class="px-6 py-6">
      <form action="/org/password" method="POST" class="space-y-6" id="password-update-form" onsubmit="console.log('Form submitting to:', this.action);">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input type="password" 
                   name="password" 
                   id="password" 
                   class="focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2 sm:text-sm border-gray-300 rounded-lg" 
                   required>
          </div>
          
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
            <input type="password" 
                   name="password_confirmation" 
                   id="password_confirmation" 
                   class="focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2 sm:text-sm border-gray-300 rounded-lg" 
                   required>
          </div>
        </div>
        
        <div class="flex justify-end">
          <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
            </svg>
            Update Password
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Organization Details Card -->
  <div class="bg-white overflow-hidden shadow rounded-lg mb-8">
    <div class="px-6 py-5 border-b border-gray-200">
      <h3 class="text-lg font-medium leading-6 text-gray-900">Organization Information</h3>
    </div>
    <div class="bg-white px-6 py-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="space-y-1">
          <dt class="text-sm font-medium text-gray-500">Organization Name</dt>
          <dd class="text-base text-gray-900 font-medium">{{ $organization->name }}</dd>
        </div>
        <div class="space-y-1">
          <dt class="text-sm font-medium text-gray-500">Email Address</dt>
          <dd class="text-base text-gray-900">{{ $organization->email }}</dd>
        </div>
        <div class="space-y-1">
          <dt class="text-sm font-medium text-gray-500">Organization Type</dt>
          <dd class="text-base text-gray-900">{{ $organization->type->name ?? '—' }}</dd>
        </div>
        <div class="space-y-1">
          <dt class="text-sm font-medium text-gray-500">Account Status</dt>
          <dd>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $organization->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
              {{ ucfirst($organization->status) }}
            </span>
          </dd>
        </div>
        <div class="space-y-1">
          <dt class="text-sm font-medium text-gray-500">Consent Label</dt>
          <dd class="text-base text-gray-900">{{ $organization->consent_label ?: 'Not Set' }}</dd>
        </div>
        <div class="space-y-1">
          <dt class="text-sm font-medium text-gray-500">Per Consent Cost</dt>
          <dd class="text-base text-gray-900">₹{{ number_format($organization->per_consent_amount ?? 0, 2) }}</dd>
        </div>
        <div class="space-y-1">
          <dt class="text-sm font-medium text-gray-500">Member Since</dt>
          <dd class="text-sm text-gray-900">{{ $organization->created_at?->format('F j, Y') }}</dd>
        </div>
        <div class="space-y-1">
          <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
          <dd class="text-sm text-gray-900">{{ $organization->updated_at?->format('F j, Y \a\t h:i A') }}</dd>
        </div>
      </div>
    </div>
  </div>

  <!-- Balance and QR Code Section -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Add Balance Card -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="px-6 py-5 border-b border-gray-200">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Add Balance</h3>
      </div>
      <div class="px-6 py-6">
        <div class="space-y-4">
          <div>
            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (₹)</label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm font-medium">₹</span>
              </div>
              <input type="number" 
                     name="amount" 
                     id="amount" 
                     class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-12 py-3 sm:text-sm border-gray-300 rounded-lg" 
                     placeholder="0.00" 
                     min="1" 
                     step="0.01" 
                     required>
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">INR</span>
              </div>
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
          
          <div>
            <button type="button" id="payWithRazorpay" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
              <svg class="-ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
              </svg>
              Pay with Razorpay
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Visual Separator -->
    <div class="my-8 border-t border-gray-200"></div>

    <!-- Public Consent Form QR Code -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="px-6 py-5 border-b border-gray-200">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Public Consent Form</h3>
        <p class="text-sm text-gray-500 mt-1">Share this QR code for users to submit consents</p>
      </div>
      <div class="px-6 py-6">
        <div class="flex flex-col items-center">
          @php($consentUrl = route('consent.form', $organization->id))
          <div class="mb-4 p-2 bg-white rounded-lg border border-gray-200">
            <img class="h-40 w-40" src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=10&data={{ urlencode($consentUrl) }}" alt="Consent Form QR Code">
          </div>
          <p class="text-sm text-gray-500 text-center mb-4">Scan this QR code or use the link below to access the public consent form</p>
          <div class="w-full">
            <div class="flex rounded-md shadow-sm">
              <input type="text" readonly value="{{ $consentUrl }}" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border border-gray-300 text-sm bg-gray-50">
              <a href="{{ $consentUrl }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Open
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Current Balance and Actions -->
  <div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="px-6 py-5 border-b border-gray-200">
      <h3 class="text-lg font-medium leading-6 text-gray-900">Account Summary</h3>
    </div>
    <div class="px-6 py-6">
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <div class="mb-4 sm:mb-0">
          <p class="text-sm font-medium text-gray-500">Current Balance</p>
          <p class="text-3xl font-bold text-gray-900">₹{{ number_format($organization->transaction_amount ?? 0, 2) }}</p>
          <p class="mt-1 text-sm text-gray-500">Updated just now</p>
        </div>
        <div class="flex flex-wrap space-x-3">
          <a href="{{ route('org.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Back to Dashboard
          </a>
          <a href="{{ route('org.balance.history') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Balance History
          </a>
          <a href="{{ route('org.transactions.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
              <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
            </svg>
            View Transactions
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

@if(session('success'))
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Show success message
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg z-50';
    toast.role = 'alert';
    toast.innerHTML = `
      <div class="flex items-center">
        <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <p class="font-medium">${'{{ session('success') }}'}</p>
      </div>
    `;
    document.body.appendChild(toast);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
      toast.remove();
    }, 5000);
  });
</script>
@endif

@if($errors->any())
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Show error message
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg z-50';
    toast.role = 'alert';
    toast.innerHTML = `
      <div class="flex items-center">
        <svg class="h-5 w-5 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <p class="font-medium">${'{{ $errors->first() }}'}</p>
      </div>
    `;
    document.body.appendChild(toast);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
      toast.remove();
    }, 5000);
  });
</script>
@endif

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
                    color: '#2563EB'
                },
                modal: {
                    ondismiss: function() {
                        payButton.disabled = false;
                        payButton.innerHTML = '<svg class="-ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>Pay with Razorpay';
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
            payButton.innerHTML = '<svg class="-ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>Pay with Razorpay';
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
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg z-50';
                toast.role = 'alert';
                toast.innerHTML = `
                  <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-medium">Balance added successfully! New balance: ₹${data.new_balance}</p>
                  </div>
                `;
                document.body.appendChild(toast);
                
                // Auto-remove after 5 seconds
                setTimeout(() => {
                  toast.remove();
                }, 5000);
                
                // Reset form
                amountInput.value = '';
                payButton.disabled = false;
                payButton.innerHTML = '<svg class="-ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>Pay with Razorpay';
                
                // Reload page to show updated balance
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                alert('Payment verification failed: ' + data.error);
                payButton.disabled = false;
                payButton.innerHTML = '<svg class="-ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>Pay with Razorpay';
            }
        } catch (error) {
            alert('Error verifying payment: ' + error.message);
            payButton.disabled = false;
            payButton.innerHTML = '<svg class="-ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>Pay with Razorpay';
        }
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Force form action to be correct
    const passwordForm = document.getElementById('password-update-form');
    if (passwordForm) {
        passwordForm.action = '/org/password';
        console.log('Password form action set to:', passwordForm.action);
    }
});
</script>
@endsection
