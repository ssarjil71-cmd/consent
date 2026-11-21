<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class BalanceController extends Controller
{
    /**
     * Show the add balance form
     */
    public function showAddBalance()
    {
        $organization = Auth::guard('organization')->user();
        
        // Initialize Razorpay
        $razorpayKey = config('services.razorpay.key');
        
        return view('organization.balance.add', compact('organization', 'razorpayKey'));
    }

    /**
     * Create Razorpay order
     */
    public function createOrder(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('createOrder called', [
            'request_data' => $request->all(),
            'user_authenticated' => Auth::guard('organization')->check(),
            'user_id' => Auth::guard('organization')->id()
        ]);

        $request->validate([
            'amount' => 'required|numeric|min:1|max:100000'
        ]);

        try {
            $amount = $request->amount * 100; // Convert to paise
            $organization = Auth::guard('organization')->user();
            
            \Log::info('Authentication passed', [
                'organization_id' => $organization->id,
                'organization_name' => $organization->name
            ]);
            
            // Check if Razorpay credentials are configured
            $razorpayKey = config('services.razorpay.key');
            $razorpaySecret = config('services.razorpay.secret');
            
            \Log::info('Razorpay config check', [
                'key_exists' => !empty($razorpayKey),
                'secret_exists' => !empty($razorpaySecret),
                'key_preview' => $razorpayKey ? substr($razorpayKey, 0, 8) . '...' : 'Not set'
            ]);
            
            if (empty($razorpayKey) || empty($razorpaySecret)) {
                \Log::error('Razorpay credentials missing');
                return response()->json([
                    'error' => 'Razorpay credentials not configured. Please add RAZORPAY_KEY and RAZORPAY_SECRET to your environment variables.'
                ], 400);
            }
            
            // Create Razorpay order
            \Log::info('Creating Razorpay order...');
            $api = new Api($razorpayKey, $razorpaySecret);
            
            $order = $api->order->create([
                'receipt' => 'BAL_' . strtoupper(Str::random(12)),
                'amount' => $amount,
                'currency' => 'INR',
                'notes' => [
                    'organization_id' => $organization->id,
                    'type' => 'balance_addition'
                ]
            ]);

            \Log::info('Razorpay order created successfully', [
                'order_id' => $order->id,
                'amount' => $amount
            ]);

            // Store order details in session
            session([
                'razorpay_order_id' => $order->id,
                'razorpay_amount' => $request->amount
            ]);

            return response()->json([
                'order_id' => $order->id,
                'amount' => $request->amount,
                'razorpay_key' => $razorpayKey
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'error' => 'Validation failed: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Razorpay order creation failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to create payment order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm balance addition after Razorpay payment
     */
    public function confirmAddBalance(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string'
        ]);

        $organization = Auth::guard('organization')->user();
        
        // Verify payment signature
        $signature = hash_hmac('sha256', $request->razorpay_order_id . '|' . $request->razorpay_payment_id, config('services.razorpay.secret'));
        
        if ($signature !== $request->razorpay_signature) {
            return response()->json(['error' => 'Invalid payment signature'], 400);
        }

        // Get amount from session
        $amount = session('razorpay_amount');
        
        if (!$amount) {
            return response()->json(['error' => 'Session expired'], 400);
        }

        // Update organization balance
        $newBalance = $organization->transaction_amount + $amount;
        $organization->transaction_amount = $newBalance;
        $organization->save();

        // Create transaction record
        Transaction::create([
            'organization_id' => $organization->id,
            'consent_id' => null,
            'amount' => $amount,
            'type' => 'credit',
            'description' => "Balance added - Razorpay Payment ID: {$request->razorpay_payment_id}",
            'balance_after' => $newBalance,
        ]);

        // Clear session
        session()->forget(['razorpay_order_id', 'razorpay_amount']);

        return response()->json([
            'success' => true,
            'message' => 'Balance added successfully!',
            'new_balance' => $newBalance
        ]);
    }

    /**
     * Show balance history
     */
    public function history()
    {
        $organization = Auth::guard('organization')->user();
        
        $transactions = Transaction::where('organization_id', $organization->id)
            ->where('type', 'credit')
            ->whereNull('consent_id') // Only balance additions, not consent deductions
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('organization.balance.history', compact('transactions', 'organization'));
    }

    /**
     * Test Razorpay configuration
     */
    public function testRazorpay()
    {
        $razorpayKey = config('services.razorpay.key');
        $razorpaySecret = config('services.razorpay.secret');
        
        $status = [
            'razorpay_key_set' => !empty($razorpayKey),
            'razorpay_secret_set' => !empty($razorpaySecret),
            'razorpay_key_value' => $razorpayKey ? substr($razorpayKey, 0, 8) . '...' : 'Not set',
            'razorpay_secret_value' => $razorpaySecret ? '***SECRET***' : 'Not set',
        ];
        
        if (!empty($razorpayKey) && !empty($razorpaySecret)) {
            try {
                $api = new Api($razorpayKey, $razorpaySecret);
                // Test API connection by fetching account details
                $account = $api->account->fetch();
                $status['api_connection'] = 'Success';
                $status['account_name'] = $account->name ?? 'N/A';
            } catch (\Exception $e) {
                $status['api_connection'] = 'Failed: ' . $e->getMessage();
            }
        } else {
            $status['api_connection'] = 'Not tested - credentials missing';
        }
        
        return response()->json($status);
    }

    /**
     * Debug route to test basic functionality
     */
    public function debug()
    {
        return response()->json([
            'status' => 'ok',
            'user_authenticated' => Auth::guard('organization')->check(),
            'user_id' => Auth::guard('organization')->id(),
            'organization' => Auth::guard('organization')->user() ? [
                'id' => Auth::guard('organization')->user()->id,
                'name' => Auth::guard('organization')->user()->name,
                'email' => Auth::guard('organization')->user()->email,
            ] : null,
            'razorpay_config' => [
                'key_set' => !empty(config('services.razorpay.key')),
                'secret_set' => !empty(config('services.razorpay.secret')),
            ]
        ]);
    }
}
