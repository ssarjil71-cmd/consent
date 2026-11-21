<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Consent;
use App\Models\Person;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use ZipArchive;

class OrganizationDashboardController extends Controller
{
    public function index()
    {
        $organization = Auth::guard('organization')->user();

        // Derive a safe slug from the organization type name (fallback if slug column not present)
        $typeSlug = null;
        if ($organization && $organization->relationLoaded('type') || $organization?->type) {
            $typeName = $organization->type->slug ?? $organization->type->name ?? 'organization';
            $typeSlug = Str::slug($typeName);
        } else {
            $typeSlug = 'organization';
        }

        // Default metrics
        $todayConsents = 0;
        $totalConsents = 0;
        $approvedConsents = 0;
        $pendingConsents = 0;
        $rejectedConsents = 0;
        $totalPersons = 0;

        // Try to compute from Consent model if available
        try {
            if (class_exists('App\\Models\\Consent') && Schema::hasTable('consents')) {
                $Consent = app('App\\Models\\Consent');
                $orgId = $organization->id;
                $todayConsents = $Consent::whereDate('created_at', today())
                    ->where('organization_id', $orgId)
                    ->count();
                $totalConsents = $Consent::where('organization_id', $orgId)->count();
                $approvedConsents = $Consent::where('organization_id', $orgId)->where('status', 'approved')->count();
                $pendingConsents = $Consent::where('organization_id', $orgId)->where('status', 'pending')->count();
                $rejectedConsents = $Consent::where('organization_id', $orgId)->where('status', 'rejected')->count();
            }
        } catch (\Throwable $e) {
            // Keep defaults if model/table missing
        }

        // Persons count (guard for table existence)
        try {
            if (class_exists('App\\Models\\Person') && Schema::hasTable('persons')) {
                $totalPersons = Person::where('organization_id', $organization->id)->count();
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return view('organization.dashboard', [
            'organization' => $organization,
            'typeSlug' => $typeSlug,
            'todayConsents' => $todayConsents,
            'totalConsents' => $totalConsents,
            'approvedConsents' => $approvedConsents,
            'pendingConsents' => $pendingConsents,
            'rejectedConsents' => $rejectedConsents,
            'totalPersons' => $totalPersons,
        ]);
    }

    public function profile()
    {
        $organization = Auth::guard('organization')->user();
        return view('organization.profile', compact('organization'));
    }

    public function transactions()
    {
        $organization = Auth::guard('organization')->user();

        $transactions = Transaction::where('organization_id', $organization->id)
            ->latest()
            ->paginate(10);

        return view('organization.transactions.list', compact('organization', 'transactions'));
    }

    public function consents()
    {
        $organization = Auth::guard('organization')->user();

        $filter = request('filter');

        $query = Consent::where('organization_id', $organization->id);

        switch ($filter) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'approved':
                $query->where('status', 'approved');
                break;
            case 'pending':
                $query->where('status', 'pending');
                break;
            case 'rejected':
                $query->where('status', 'rejected');
                break;
            default:
                $filter = null; // treat anything else as "no filter"
                break;
        }

        $consents = $query->latest()->paginate(10)->withQueryString();

        return view('organization.consents.index', compact('organization', 'consents', 'filter'));
    }

    public function consentShow(Consent $consent)
    {
        $organization = Auth::guard('organization')->user();
        abort_unless($consent->organization_id === $organization->id, 403);

        $persons = collect();
        if (Schema::hasTable('persons')) {
            $persons = Person::where('organization_id', $organization->id)
                ->orderBy('name')
                ->get();
        }

        return view('organization.consents.show', compact('organization', 'consent', 'persons'));
    }

    public function consentApprove(Consent $consent)
    {
        $organization = Auth::guard('organization')->user();
        abort_unless($consent->organization_id === $organization->id, 403);

        // Don't process if already approved
        if ($consent->status === 'approved') {
            return redirect()->back()->with('error', 'Consent is already approved');
        }

        request()->validate([
            'person_id' => 'nullable|exists:persons,id',
            'payment_method' => 'required_if:per_consent_amount,>0|in:balance,razorpay',
            'razorpay_payment_id' => 'required_if:payment_method,razorpay|string',
        ]);

        // Start database transaction
        \DB::beginTransaction();

        try {
            $personId = request('person_id');
            if ($personId) {
                $person = Person::where('organization_id', $organization->id)->findOrFail($personId);
                $consent->person_id = $person->id;
            }
            
            // Debug: Log the current organization data before any changes
            \Log::info('Organization before refresh', [
                'organization_id' => $organization->id,
                'per_consent_amount' => $organization->per_consent_amount,
                'transaction_amount' => $organization->transaction_amount,
                'consent_id' => $consent->id,
                'organization_data' => $organization->toArray()
            ]);

            // Refresh organization data to get latest balance from database
            $organization->refresh();
            
            // Debug: Log after refresh
            \Log::info('Organization after refresh', [
                'organization_id' => $organization->id,
                'per_consent_amount' => $organization->per_consent_amount,
                'transaction_amount' => $organization->transaction_amount
            ]);
            
            // Get per consent amount (ensure it's a valid number)
            $perConsentAmount = floatval($organization->per_consent_amount ?? 0);
            $currentBalance = floatval($organization->transaction_amount ?? 0);
            $paymentMethod = request('payment_method', 'balance');
            
            // Check for 7-day payment protection
            $shouldDeductPayment = true;
            if ($perConsentAmount > 0) {
                $shouldDeductPayment = $this->shouldDeductPayment($consent, $organization);
                
                \Log::info('Payment protection check', [
                    'consent_id' => $consent->id,
                    'name' => $consent->name,
                    'phone' => $consent->phone,
                    'should_deduct_payment' => $shouldDeductPayment ? 'yes' : 'no',
                    'per_consent_amount' => $perConsentAmount,
                    'method_called' => 'shouldDeductPayment called successfully'
                ]);
            }
            
            // Debug log before payment processing
            \Log::info('Before payment processing', [
                'organization_id' => $organization->id,
                'per_consent_amount' => $perConsentAmount,
                'current_balance' => $currentBalance,
                'payment_method' => $paymentMethod,
                'consent_id' => $consent->id,
                'should_deduct_payment' => $shouldDeductPayment,
                'is_per_consent_amount_gt_zero' => ($perConsentAmount > 0) ? 'yes' : 'no'
            ]);
            
            if ($perConsentAmount > 0) {
                if ($shouldDeductPayment) {
                    if ($paymentMethod === 'balance') {
                        // Check balance sufficiency
                        if ($currentBalance < $perConsentAmount) {
                            throw new \Exception('Insufficient balance to approve this consent. Current balance: ₹' . number_format($currentBalance, 2) . 
                                          ', Required: ₹' . number_format($perConsentAmount, 2));
                        }
                        
                        // Calculate new balance
                        $newBalance = $currentBalance - $perConsentAmount;
                        
                        // Update the organization's balance directly
                        $organization->transaction_amount = $newBalance;
                        
                        // Save the organization using direct DB update
                        $updated = \DB::table('organizations')
                            ->where('id', $organization->id)
                            ->update([
                                'transaction_amount' => $newBalance,
                                'updated_at' => now()
                            ]);
                        
                        if ($updated === 0) {
                            throw new \Exception('Failed to update organization balance - no rows were updated');
                        }
                        
                        // Record the transaction
                        if (Schema::hasTable('transactions')) {
                            Transaction::create([
                                'organization_id' => $organization->id,
                                'consent_id' => $consent->id,
                                'amount' => -$perConsentAmount,
                                'type' => 'consent_approval_balance',
                                'description' => 'Balance deduction for approving consent #' . $consent->id,
                                'balance_after' => $newBalance,
                            ]);
                        }
                    } elseif ($paymentMethod === 'razorpay') {
                        // Verify Razorpay payment
                        $razorpayPaymentId = request('razorpay_payment_id');
                        if (!$razorpayPaymentId) {
                            throw new \Exception('Razorpay payment ID is required for Razorpay payment method.');
                        }
                        
                        // Record the transaction
                        if (Schema::hasTable('transactions')) {
                            Transaction::create([
                                'organization_id' => $organization->id,
                                'consent_id' => $consent->id,
                                'amount' => $perConsentAmount,
                                'type' => 'consent_approval_razorpay',
                                'description' => 'Razorpay payment for approving consent #' . $consent->id . ' (Payment ID: ' . $razorpayPaymentId . ')',
                                'payment_id' => $razorpayPaymentId,
                                'balance_after' => $currentBalance, // Balance unchanged for Razorpay
                            ]);
                        }
                    }
                } else {
                    // Record zero-amount transaction for 7-day protection
                    if (Schema::hasTable('transactions')) {
                        Transaction::create([
                            'organization_id' => $organization->id,
                            'consent_id' => $consent->id,
                            'amount' => 0,
                            'type' => 'consent_approval_protected',
                            'description' => 'Payment protected for consent #' . $consent->id . ' (7-day rule)',
                            'balance_after' => $currentBalance,
                        ]);
                    }
                }
            }
            
            // Update consent status
            $consent->status = 'approved';
            $consent->approved_at = now();
            $consent->save();

            // Commit the transaction
            \DB::commit();

            return redirect()->route('org.consents.show', $consent->id)
                ->with('success', 'Consent approved successfully. ' . 
                    ($perConsentAmount > 0 ? '₹' . number_format($perConsentAmount, 2) . ' has been deducted from your balance.' : ''));
                
        } catch (\Exception $e) {
            // Rollback the transaction on error
            \DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to approve consent: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function consentPdf(Consent $consent)
    {
        $organization = Auth::guard('organization')->user();
        abort_unless($consent->organization_id === $organization->id, 403);

        if ($consent->status !== 'approved') {
            return redirect()->back()->with('error', 'PDF can only be downloaded for approved consents.');
        }

        $reqLang = request('lang');
        $lang = $reqLang ? strtolower($reqLang) : null;
        if ($lang && !in_array($lang, ['en','hi','mr'])) { $lang = 'en'; }

        $translationsMap = [
            'en' => [
                'consent' => 'Consent',
                'generated' => 'Generated',
                'name' => 'Name',
                'email' => 'Email',
                'phone' => 'Phone',
                'status' => 'Status',
                'submitted' => 'Submitted',
                'updated' => 'Last Updated',
                'notes' => 'Notes',
                'risk_acknowledgement_title' => 'Risk Acknowledgement',
                'risk_acknowledgement_label' => 'Risk Acknowledgement Status',
                'otp_code' => 'OTP Code',
                'otp_verified_at' => 'OTP Verified At',
                'kyc_document_type' => 'KYC Document Type',
                'kyc_document_number' => 'Document Number',
                'kyc_verification_method' => 'Verification Method',
                'kyc_verified_at' => 'KYC Verified At',
                'footer' => 'Generated by ConsentRX • This document may contain sensitive personal data. Please handle as per DPDP Act 2023.',
            ],
            'hi' => [
                'consent' => 'सहमति',
                'generated' => 'निर्मित',
                'name' => 'नाम',
                'email' => 'ईमेल',
                'phone' => 'फोन',
                'status' => 'स्थिति',
                'submitted' => 'जमा दिनांक',
                'updated' => 'अंतिम अद्यतन',
                'notes' => 'टिप्पणी',
                'risk_acknowledgement_title' => 'जोखिम स्वीकारोक्ति',
                'risk_acknowledgement_label' => 'जोखिम स्वीकारोक्ति स्थिति',
                'otp_code' => 'OTP कोड',
                'otp_verified_at' => 'OTP सत्यापित समय',
                'footer' => 'ConsentRX द्वारा जनित • यह दस्तावेज़ संवेदनशील व्यक्तिगत डेटा रख सकता है। कृपया DPDP अधिनियम 2023 के अनुसार संभालें।',
            ],
            'mr' => [
                'consent' => 'संमती',
                'generated' => 'निर्मित',
                'name' => 'नाव',
                'email' => 'ईमेल',
                'phone' => 'फोन',
                'status' => 'स्थिती',
                'submitted' => 'सादर दिनांक',
                'updated' => 'शेवटचे अद्यतन',
                'notes' => 'नोंदी',
                'risk_acknowledgement_title' => 'जोखीम स्वीकृती',
                'risk_acknowledgement_label' => 'जोखीम स्वीकृती स्थिती',
                'otp_code' => 'OTP कोड',
                'otp_verified_at' => 'OTP पडताळणी वेळ',
                'footer' => 'ConsentRX द्वारे तयार • या दस्तऐवजात संवेदनशील वैयक्तिक माहिती असू शकते. कृपया DPDP कायदा 2023 नुसार हाताळा.',
            ],
        ];

        // Determine which languages to render in the PDF
        $langs = [];
        if ($lang) {
            $langs = [$lang];
        } else {
            $formLang = in_array($consent->lang, ['en','hi','mr']) ? $consent->lang : 'en';
            $langs = $formLang === 'en' ? ['en'] : ['en', $formLang];
        }

        $html = view('organization.consents.pdf', [
            'organization' => $organization,
            'consent' => $consent,
            'translationsMap' => $translationsMap,
            'langs' => $langs,
        ])->render();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);
        $mpdf->SetTitle('Consent #'.$consent->id);
        $mpdf->WriteHTML($html);

        $filename = 'consent_'.$consent->id.'.pdf';
        return response($mpdf->Output($filename, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    /**
     * Add balance to the organization's account
     */
    public function addBalance(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        $organization = Auth::guard('organization')->user();
        $amount = $request->input('amount');

        // Start database transaction
        DB::beginTransaction();
        try {
            // Update the organization's balance
            $organization->increment('transaction_amount', $amount);

            // Create a transaction record
            Transaction::create([
                'organization_id' => $organization->id,
                'amount' => $amount,
                'type' => 'credit',
                'description' => 'Balance added via dashboard',
                'status' => 'completed'
            ]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Successfully added ₹' . number_format($amount, 2) . ' to your balance');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding balance: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Failed to add balance. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update organization profile information
     */
    public function updateProfile(Request $request)
    {
        $organization = Auth::guard('organization')->user();
        
        $request->validate([
            'email' => 'required|email|unique:organizations,email,' . $organization->id,
            'name' => 'required|string|max:255',
        ]);

        try {
            $organization->update([
                'email' => $request->email,
                'name' => $request->name,
            ]);

            return redirect()
                ->route('org.profile')
                ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Failed to update profile. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update organization password
     */
    public function updatePassword(Request $request)
    {
        $organization = Auth::guard('organization')->user();
        
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            $organization->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()
                ->route('org.profile')
                ->with('success', 'Password updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating password: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Failed to update password. Please try again.')
                ->withInput();
        }
    }

    /**
     * Check if payment should be deducted based on 7-day protection rule
     */
    private function shouldDeductPayment($consent, $organization)
    {
        // Check if there's any approved consent with same name and phone within last 7 days
        $sevenDaysAgo = now()->subDays(7);
        
        \Log::info('Checking payment protection', [
            'current_consent_id' => $consent->id,
            'name' => $consent->name,
            'phone' => $consent->phone,
            'organization_id' => $organization->id,
            'seven_days_ago' => $sevenDaysAgo->format('Y-m-d H:i:s')
        ]);
        
        $existingConsent = Consent::where('organization_id', $organization->id)
            ->where('name', $consent->name)
            ->where('phone', $consent->phone)
            ->where('status', 'approved')
            ->where('created_at', '>=', $sevenDaysAgo)
            ->where('id', '!=', $consent->id) // Exclude current consent
            ->first();
            
        \Log::info('Existing consent found', [
            'found' => $existingConsent ? 'yes' : 'no',
            'existing_consent_id' => $existingConsent?->id,
            'existing_created_at' => $existingConsent?->created_at?->format('Y-m-d H:i:s')
        ]);
            
        // If found, don't deduct payment (7-day protection)
        return !$existingConsent;
    }

    public function consentBackup(Consent $consent)
    {
        // Verify organization owns this consent
        $organization = Auth::guard('organization')->user();
        abort_unless($consent->organization_id === $organization->id, 403);

        $zip = new ZipArchive();
        $zipFileName = 'consent_' . $consent->id . '_backup_' . date('Y-m-d_H-i-s') . '.zip';
        $zipFilePath = storage_path('app/temp/' . $zipFileName);

        // Create temp directory if it doesn't exist
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // Add PDF if exists
            $pdfPath = storage_path('app/public/consents/consent_' . $consent->id . '.pdf');
            if (file_exists($pdfPath)) {
                $zip->addFile($pdfPath, 'consent_' . $consent->id . '.pdf');
            }

            // Add signature upload
            if ($consent->signature_upload_path) {
                $signaturePath = storage_path('app/public/' . $consent->signature_upload_path);
                if (file_exists($signaturePath)) {
                    $zip->addFile($signaturePath, 'signature_' . basename($consent->signature_upload_path));
                }
            }

            // Add thumb upload
            if ($consent->thumb_upload_path) {
                $thumbPath = storage_path('app/public/' . $consent->thumb_upload_path);
                if (file_exists($thumbPath)) {
                    $zip->addFile($thumbPath, 'thumb_' . basename($consent->thumb_upload_path));
                }
            }

            // Add selfie upload
            if ($consent->selfie_upload_path) {
                $selfiePath = storage_path('app/public/' . $consent->selfie_upload_path);
                if (file_exists($selfiePath)) {
                    $zip->addFile($selfiePath, 'selfie_' . basename($consent->selfie_upload_path));
                }
            }

            // Add ID proof upload
            if ($consent->id_proof_upload_path) {
                $idProofPath = storage_path('app/public/' . $consent->id_proof_upload_path);
                if (file_exists($idProofPath)) {
                    $zip->addFile($idProofPath, 'id_proof_' . basename($consent->id_proof_upload_path));
                }
            }

            // Add thumb video
            if ($consent->thumb_video_path) {
                $videoPath = storage_path('app/public/' . $consent->thumb_video_path);
                if (file_exists($videoPath)) {
                    $zip->addFile($videoPath, 'thumb_video_' . basename($consent->thumb_video_path));
                }
            }

            // Add witness signature
            if ($consent->witness_signature_path) {
                $witnessPath = storage_path('app/public/' . $consent->witness_signature_path);
                if (file_exists($witnessPath)) {
                    $zip->addFile($witnessPath, 'witness_signature_' . basename($consent->witness_signature_path));
                }
            }

            // Add consent info text file
            $consentInfo = "Consent ID: " . $consent->id . "\n";
            $consentInfo .= "Name: " . $consent->name . "\n";
            $consentInfo .= "Email: " . ($consent->email ?: 'N/A') . "\n";
            $consentInfo .= "Phone: " . ($consent->phone ?: 'N/A') . "\n";
            $consentInfo .= "Status: " . $consent->status . "\n";
            $consentInfo .= "Created: " . $consent->created_at->format('Y-m-d H:i:s') . "\n";
            $consentInfo .= "Organization: " . ($consent->organization ? $consent->organization->name : 'N/A') . "\n";
            $zip->addFromString('consent_info.txt', $consentInfo);

            $zip->close();

            // Download the file and then delete it
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'Failed to create backup file.');
    }
}
