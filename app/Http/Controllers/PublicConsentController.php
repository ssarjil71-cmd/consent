<?php

namespace App\Http\Controllers;

use App\Models\Consent;
use App\Models\Organization;
use App\Models\RiskConsent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PublicConsentController extends Controller
{
    public function show(Organization $organization)
    {
        $lang = strtolower(request('lang', 'en'));
        if (!in_array($lang, ['en','hi','mr'])) { $lang = 'en'; }

        $slug = \Illuminate\Support\Str::slug(strtolower($organization->type->slug ?? $organization->type->name ?? ''));
        $entity = trim($organization->consent_label ?? '') ?: match ($slug) {
            'hospital' => 'Patient',
            'yoga' => 'Participant',
            'zumba' => 'Member',
            'wellness' => 'Client',
            default => 'Member',
        };

        $view = "public.consent.$slug";
        if (!view()->exists($view)) {
            $view = 'public.consent.default';
        }

        $entityLower = \Illuminate\Support\Str::lower($entity);

        $translations = [
            'en' => [
                'title' => "{$entity} Consent Form",
                'name' => 'Name',
                'email' => 'Email',
                'phone' => 'Phone',
                'notes' => 'Notes',
                'submit' => 'Submit Consent',
                'choose_language' => 'Choose Language',
                'welcome_title' => 'Welcome!',
                'welcome_body' => "We're glad you're here. This quick form helps us capture your {$entityLower} consent securely. Review the details and continue when you're ready.",
                'consent_for_title' => 'Who is this consent for?',
                'consent_for_body' => 'Please select whether you are giving consent for yourself or a relative.',
                'consent_for_self' => 'Myself',
                'consent_for_self_help' => 'Choose this if you are submitting your own consent.',
                'consent_for_relative' => 'My relative',
                'consent_for_relative_help' => 'Choose this if you are submitting on behalf of a relative.',
                'relative_details_title' => 'Relative details',
                "relative_details_body" => "Please share the relative's name and your relation for our records.",
                'relative_name_label' => "Relative name",
                "relative_name_placeholder" => "Enter relative's full name",
                'relative_relation_label' => 'Relation',
                'relative_relation_placeholder' => 'e.g. Mother',
                'age' => 'Age',
                'age_placeholder' => 'Enter age',
                'sex' => 'Sex',
                'sex_placeholder' => 'Select sex',
                'sex_options' => [
                    'male' => 'Male',
                    'female' => 'Female',
                    'other' => 'Other',
                    'prefer_not_to_say' => 'Prefer not to say',
                ],
                'address' => 'Address',
                'address_placeholder' => 'Enter full address',
                'registration_number' => 'Registration No.',
                'registration_number_placeholder' => 'Enter registration number',
                'consent_history_title' => "{$entity} Consent Details",
                'consent_history_empty' => 'No consents have been submitted yet.',
                'consent_for_label' => 'Consent for',
                'status_label' => 'Status',
                'submitted_at' => 'Submitted on',
                'terms_title' => 'Terms & Conditions',
                'terms_description' => 'Please review the following terms before submitting your consent.',
                'terms_acknowledgement_label' => 'I have read and agree to the Terms & Conditions listed above.',
                'liability_title' => 'Liability Release',
                'liability_description' => 'Please review the following release before submitting your consent.',
                'liability_acknowledgement_label' => 'I have read and agree to the Liability Release listed above.',
                'risk_acknowledgement_title' => 'Risk Acknowledgement',
                'risk_acknowledgement_description' => 'Please confirm you acknowledge the risks outlined below.',
                'risk_acknowledgement_label' => 'I acknowledge the risks shared above.',
                'yes' => 'Yes',
                'no' => 'No',
            ],
            'hi' => [
                'title' => "{$entity} सहमति फॉर्म",
                'name' => 'नाम',
                'email' => 'ईमेल',
                'phone' => 'फोन',
                'notes' => 'टिप्पणी',
                'submit' => 'सहमति सबमिट करें',
                'choose_language' => 'भाषा चुनें',
                'welcome_title' => 'स्वागत है!',
                'welcome_body' => "हमें खुशी है कि आप यहाँ हैं। यह त्वरित फ़ॉर्म हमें आपकी {$entityLower} सहमति को सुरक्षित रूप से रिकॉर्ड करने में मदद करता है। विवरण देखें और तैयार होने पर आगे बढ़ें।",
                'consent_for_title' => 'यह सहमति किसके लिए है?',
                'consent_for_body' => 'कृपया चुनें कि आप अपने लिए सहमति दे रहे हैं या किसी परिजन के लिए।',
                'consent_for_self' => 'खुद के लिए',
                'consent_for_self_help' => 'यदि आप अपनी सहमति दे रहे हैं तो इसे चुनें।',
                'consent_for_relative' => 'परिजन के लिए',
                'consent_for_relative_help' => 'यदि आप किसी परिजन की सहमति जमा कर रहे हैं तो इसे चुनें।',
                'relative_details_title' => 'परिजन का विवरण',
                'relative_details_body' => 'कृपया परिजन का नाम और आपसे संबंध साझा करें।',
                'relative_name_label' => 'परिजन का नाम',
                'relative_name_placeholder' => 'परिजन का पूरा नाम दर्ज करें',
                'relative_relation_label' => 'रिश्ता',
                'relative_relation_placeholder' => 'जैसे: माता',
                'age' => 'आयु',
                'age_placeholder' => 'आयु दर्ज करें',
                'sex' => 'लिंग',
                'sex_placeholder' => 'लिंग चुनें',
                'sex_options' => [
                    'male' => 'पुरुष',
                    'female' => 'महिला',
                    'other' => 'अन्य',
                    'prefer_not_to_say' => 'बताना नहीं चाहते',
                ],
                'address' => 'पता',
                'address_placeholder' => 'पूरा पता दर्ज करें',
                'registration_number' => 'पंजीकरण संख्या',
                'registration_number_placeholder' => 'पंजीकरण संख्या दर्ज करें',
                'consent_history_title' => "{$entity} सहमति विवरण",
                'consent_history_empty' => 'अभी तक कोई सहमति जमा नहीं की गई है।',
                'consent_for_label' => 'सहमति किसके लिए',
                'status_label' => 'स्थिति',
                'submitted_at' => 'जमा किया गया',
                'terms_title' => 'Terms & Conditions',
                'terms_description' => 'कृपया सहमति जमा करने से पहले नीचे दी गई शर्तों को पढ़ें।',
                'terms_acknowledgement_label' => 'मैंने ऊपर सूचीबद्ध Terms & Conditions पढ़ लिये हैं और उनसे सहमत हूं।',
                'liability_title' => 'दायित्व विमोचन',
                'liability_description' => 'कृपया सहमति जमा करने से पहले नीचे दिए गए दायित्व विमोचन को पढ़ें।',
                'liability_acknowledgement_label' => 'मैंने ऊपर दिए गए दायित्व विमोचन को पढ़ लिया है और उससे सहमत हूं।',
                'risk_acknowledgement_title' => 'जोखिम स्वीकारोक्ति',
                'risk_acknowledgement_description' => 'कृपया नीचे दिए गए जोखिमों को स्वीकार करने की पुष्टि करें।',
                'risk_acknowledgement_label' => 'मैं ऊपर बताए गए जोखिमों को स्वीकार करता/करती हूँ।',
                'yes' => 'हाँ',
                'no' => 'नहीं',
            ],
            'mr' => [
                'title' => "{$entity} संमती फॉर्म",
                'name' => 'नाव',
                'email' => 'ईमेल',
                'phone' => 'फोन',
                'notes' => 'नोंदी',
                'submit' => 'संमती सबमिट करा',
                'choose_language' => 'भाषा निवडा',
                'welcome_title' => 'स्वागत आहे!',
                'welcome_body' => "आपण येथे आलात याचा आम्हाला आनंद आहे. हा जलद फॉर्म आम्हाला आपली {$entityLower} संमती सुरक्षितपणे नोंदवायला मदत करतो. तपशील वाचा आणि तयार झाल्यावर पुढे जा.",
                'consent_for_title' => 'ही संमती कोणासाठी आहे?',
                'consent_for_body' => 'कृपया निवडा की आपण स्वतःसाठी संमती देत आहात की नातेवाईकासाठी.',
                'consent_for_self' => 'स्वतःसाठी',
                'consent_for_self_help' => 'आपण स्वतःची संमती देत असल्यास हे निवडा.',
                'consent_for_relative' => 'नातेवाईकासाठी',
                'consent_for_relative_help' => 'नातेवाईकाच्या वतीने संमती देत असल्यास हे निवडा.',
                'relative_details_title' => 'नातेवाईकाची माहिती',
                'relative_details_body' => 'कृपया नातेवाईकाचे नाव आणि तुमचा संबंध सांगा.',
                'relative_name_label' => 'नातेवाईकाचे नाव',
                'relative_name_placeholder' => 'नातेवाईकाचे पूर्ण नाव लिहा',
                'relative_relation_label' => 'नातेवाईकाचा संबंध',
                'relative_relation_placeholder' => 'उदा. आई',
                'age' => 'वय',
                'age_placeholder' => 'वय प्रविष्ट करा',
                'sex' => 'लिंग',
                'sex_placeholder' => 'लिंग निवडा',
                'sex_options' => [
                    'male' => 'पुरुष',
                    'female' => 'महिला',
                    'other' => 'इतर',
                    'prefer_not_to_say' => 'सांगू इच्छित नाही',
                ],
                'address' => 'पत्ता',
                'address_placeholder' => 'संपूर्ण पत्ता प्रविष्ट करा',
                'registration_number' => 'नोंदणी क्रमांक',
                'registration_number_placeholder' => 'नोंदणी क्रमांक प्रविष्ट करा',
                'consent_history_title' => "{$entity} संमती तपशील",
                'consent_history_empty' => 'अद्याप कोणतीही संमती सबमिट केलेली नाही.',
                'consent_for_label' => 'संमती कोणासाठी',
                'status_label' => 'स्थिती',
                'submitted_at' => 'सबमिट दिनांक',
                'terms_title' => 'Terms & Conditions',
                'terms_description' => 'कृपया संमती सादर करण्यापूर्वी खालील अटी वाचा.',
                'terms_acknowledgement_label' => 'मी वरील Terms & Conditions वाचल्या आहेत आणि त्यांना सहमती देतो/देते.',
                'liability_title' => 'दायित्व मुक्तता',
                'liability_description' => 'कृपया संमती सादर करण्यापूर्वी खालील दायित्व मुक्तता वाचा.',
                'liability_acknowledgement_label' => 'मी वरील दायित्व मुक्तता वाचली आहे आणि त्यास मान्यता देतो/देते.',
                'risk_acknowledgement_title' => 'जोखीम स्वीकृती',
                'risk_acknowledgement_description' => 'कृपया खाली नमूद केलेल्या जोखमींची पावती द्या.',
                'risk_acknowledgement_label' => 'मी वर नमूद केलेल्या जोखमी मान्य करतो/करते.',
                'yes' => 'होय',
                'no' => 'नाही',
            ],
        ][$lang];

        $consents = Consent::query()
            ->where('organization_id', $organization->id)
            ->latest()
            ->get();

        $riskConsents = RiskConsent::query()
            ->where('organization_id', $organization->id)
            ->where('is_active', true)
            ->with('category')
            ->orderByDesc('updated_at')
            ->get();

        $riskConsentCategories = $riskConsents
            ->map(function (RiskConsent $consent) {
                return [
                    'id' => $consent->category?->id,
                    'name' => $consent->category?->name,
                ];
            })
            ->unique(function ($category) {
                return $category['id'] ?? 'uncategorized';
            })
            ->values();

        $legalLanguageLabels = [
            'en' => 'English',
            'hi' => 'Hindi',
            'mr' => 'Marathi',
        ];

        $termsAttributes = [
            'en' => 'terms_text',
            'hi' => 'terms_text_hi',
            'mr' => 'terms_text_mr',
        ];

        $liabilityAttributes = [
            'en' => 'liability_release_text',
            'hi' => 'liability_release_text_hi',
            'mr' => 'liability_release_text_mr',
        ];

        $riskAcknowledgementAttributes = [
            'en' => 'risk_acknowledgement_text',
            'hi' => 'risk_acknowledgement_text_hi',
            'mr' => 'risk_acknowledgement_text_mr',
        ];

        $termsTextByLang = [];
        $liabilityTextByLang = [];
        $riskAcknowledgementTextByLang = [];
        foreach ($termsAttributes as $code => $attribute) {
            $termsTextByLang[$code] = trim((string) ($organization?->{$attribute} ?? ''));
        }
        foreach ($liabilityAttributes as $code => $attribute) {
            $liabilityTextByLang[$code] = trim((string) ($organization?->{$attribute} ?? ''));
        }
        foreach ($riskAcknowledgementAttributes as $code => $attribute) {
            $riskAcknowledgementTextByLang[$code] = trim((string) ($organization?->{$attribute} ?? ''));
        }

        $termsClausesByLang = [];
        foreach ($termsTextByLang as $code => $text) {
            $termsClausesByLang[$code] = collect(preg_split('/\r\n|\r|\n/', $text))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values()
                ->all();
        }

        $liabilityClausesByLang = [];
        foreach ($liabilityTextByLang as $code => $text) {
            $liabilityClausesByLang[$code] = collect(preg_split('/\r\n|\r|\n/', $text))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values()
                ->all();
        }

        $riskAcknowledgementClausesByLang = [];
        foreach ($riskAcknowledgementTextByLang as $code => $text) {
            $riskAcknowledgementClausesByLang[$code] = collect(preg_split('/\r\n|\r|\n/', $text))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values()
                ->all();
        }

        $langPreferenceOrder = array_values(array_unique([$lang, 'en', 'hi', 'mr']));

        $termsClauses = [];
        $termsLangUsed = null;
        foreach ($langPreferenceOrder as $candidateLang) {
            if (!empty($termsClausesByLang[$candidateLang])) {
                $termsClauses = $termsClausesByLang[$candidateLang];
                $termsLangUsed = $candidateLang;
                break;
            }
        }

        $liabilityClauses = [];
        $liabilityLangUsed = null;
        foreach ($langPreferenceOrder as $candidateLang) {
            if (!empty($liabilityClausesByLang[$candidateLang])) {
                $liabilityClauses = $liabilityClausesByLang[$candidateLang];
                $liabilityLangUsed = $candidateLang;
                break;
            }
        }

        $riskAcknowledgementClauses = [];
        $riskAcknowledgementLangUsed = null;
        foreach ($langPreferenceOrder as $candidateLang) {
            if (!empty($riskAcknowledgementClausesByLang[$candidateLang])) {
                $riskAcknowledgementClauses = $riskAcknowledgementClausesByLang[$candidateLang];
                $riskAcknowledgementLangUsed = $candidateLang;
                break;
            }
        }

        return view($view, [
            'organization' => $organization,
            'entity' => $entity,
            'slug' => $slug,
            't' => $translations,
            'lang' => $lang,
            'consents' => $consents,
            'riskConsents' => $riskConsents,
            'riskConsentCategories' => $riskConsentCategories,
            'termsClauses' => $termsClauses,
            'liabilityClauses' => $liabilityClauses,
            'organizationTermsText' => $termsTextByLang['en'] ?? '',
            'organizationLiabilityText' => $liabilityTextByLang['en'] ?? '',
            'termsLangUsed' => $termsLangUsed,
            'liabilityLangUsed' => $liabilityLangUsed,
            'riskAcknowledgementClauses' => $riskAcknowledgementClauses,
            'riskAcknowledgementLangUsed' => $riskAcknowledgementLangUsed,
            'legalLanguageLabels' => $legalLanguageLabels,
        ]);
    }

    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $otpCode = (string) random_int(100000, 999999);

        $digitsOnlyPhone = preg_replace('/\D+/', '', $data['phone']);
        $mobileForSms = $digitsOnlyPhone;
        if (strlen($digitsOnlyPhone) === 10) {
            $mobileForSms = '91' . $digitsOnlyPhone;
        }

        $otpPayload = [
            'organization_id' => (int) $data['organization_id'],
            'phone' => $data['phone'],
            'code' => $otpCode,
            'expires_at' => time() + 300,
        ];

        session(['consent_otp' => $otpPayload]);

        $msg91Config = config('services.msg91', []);

        try {
            if (!empty($msg91Config['auth_key']) && !empty($msg91Config['template_id'])) {
                $endpoint = $msg91Config['endpoint'] ?? 'https://api.msg91.com/api/v5/otp';
                $otpExpiry = (int) ($msg91Config['otp_expiry'] ?? 5);

                $httpClient = Http::withHeaders([
                    'authkey' => $msg91Config['auth_key'],
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->timeout(10);

                if (app()->environment('local')) {
                    $httpClient = $httpClient->withoutVerifying();
                }

                $response = $httpClient->post($endpoint, [
                    'template_id' => $msg91Config['template_id'],
                    'mobile' => $mobileForSms,
                    'authkey' => $msg91Config['auth_key'],
                    'otp' => (string) $otpCode,
                    'otp_expiry' => (string) $otpExpiry,
                ]);

                $responseData = $response->json();

                \Log::info('Consent OTP sent via MSG91', [
                    'organization_id' => $otpPayload['organization_id'],
                    'phone' => $otpPayload['phone'],
                    'mobile_for_sms' => $mobileForSms,
                    'response_status' => $response->status(),
                    'response_body' => $response->body(),
                    'response_data' => $responseData,
                ]);

                if (!$response->successful() || (($responseData['type'] ?? null) !== 'success')) {
                    session()->forget('consent_otp');

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unable to send OTP at the moment. Please try again later.',
                    ], 500);
                }
            } else {
                \Log::warning('MSG91 credentials not configured; skipping SMS send for consent OTP.');
            }
        } catch (\Throwable $e) {
            \Log::error('MSG91 OTP send failed', [
                'organization_id' => $otpPayload['organization_id'],
                'phone' => $otpPayload['phone'],
                'error' => $e->getMessage(),
            ]);

            session()->forget('consent_otp');

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to send OTP at the moment. Please try again later.',
            ], 500);
        }

        \Log::info('Consent OTP generated', [
            'organization_id' => $otpPayload['organization_id'],
            'phone' => $otpPayload['phone'],
            'code' => $otpPayload['code'],
            'expires_at' => $otpPayload['expires_at'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully.',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'phone' => ['required', 'string', 'max:20'],
            'otp_code' => ['required', 'string', 'digits:6'],
        ]);

        $otpData = session('consent_otp');

        if (!$otpData || !is_array($otpData) || !isset($otpData['phone'], $otpData['code'], $otpData['expires_at'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP has expired or is not available. Please send a new OTP.',
            ], 422);
        }

        if ($otpData['phone'] !== $data['phone'] || $otpData['code'] !== $data['otp_code']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP. Please try again.',
            ], 422);
        }

        if (($otpData['expires_at'] ?? 0) < time()) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP has expired. Please request a new one.',
            ], 422);
        }

        session(['consent_otp_verified' => [
            'organization_id' => $otpData['organization_id'],
            'phone' => $otpData['phone'],
            'verified_at' => time(),
        ]]);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully.',
        ]);
    }

    public function verifyKyc(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'kyc_document_type' => ['required', 'in:pan,voter_id'],
            'kyc_input_mode' => ['required', 'in:number,image'],
            'kyc_document_number' => ['nullable', 'string', 'max:100', 'required_if:kyc_input_mode,number'],
            'kyc_document_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096', 'required_if:kyc_input_mode,image'],
        ]);

        $config = config('services.surepass', []);

        if (empty($config['base_url']) || empty($config['api_key'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'KYC service is not configured. Please try again later.',
            ], 500);
        }

        $baseUrl = rtrim((string) $config['base_url'], '/');
        $ocrEndpoint = null;
        $numberVerifyUrl = null;

        // OCR endpoints (image-based)
        if ($data['kyc_document_type'] === 'pan') {
            $ocrEndpoint = $config['pan_endpoint'] ?? null;
            $numberVerifyUrl = $baseUrl . '/api/v1/pan/pan';
        } elseif ($data['kyc_document_type'] === 'voter_id') {
            $ocrEndpoint = $config['voter_endpoint'] ?? null;
            $numberVerifyUrl = $baseUrl . '/api/v1/voter-id/voter-id';
        }

        if (empty($ocrEndpoint) && $data['kyc_input_mode'] === 'image') {
            return response()->json([
                'status' => 'error',
                'message' => 'KYC OCR endpoint is not configured.',
            ], 500);
        }

        try {
            $http = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . (string) $config['api_key'],
                'Accept' => 'application/json',
            ])->timeout(15);

            $response = null;

            if ($data['kyc_input_mode'] === 'number') {
                if (!$numberVerifyUrl) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Number-based KYC endpoint is not configured for this document type.',
                    ], 500);
                }

                $payload = [
                    'id_number' => $data['kyc_document_number'],
                ];

                $response = $http->post($numberVerifyUrl, $payload);
            } else {
                // Image-based OCR verification using SurePass.
                $file = $request->file('kyc_document_image');

                if (!$file) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Document image is required for KYC.',
                    ], 422);
                }
                $fieldName = $data['kyc_document_type'] === 'license' ? 'front' : 'file';

                $url = $baseUrl . '/' . ltrim((string) $ocrEndpoint, '/');

                $response = $http->attach(
                    $fieldName,
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post($url);
            }

            $statusCode = $response->status();
            $body = $response->json();

            $successFlag = is_array($body) && array_key_exists('success', $body)
                ? (bool) $body['success']
                : null;

            // Treat as success only when HTTP is OK and success flag is true or not provided.
            if ($response->successful() && ($successFlag === null || $successFlag === true)) {
                return response()->json([
                    'status' => 'success',
                    'message' => $body['message'] ?? 'KYC verified successfully.',
                    'provider_status' => $body,
                ]);
            }

            $rawBody = $body;
            if (!is_array($rawBody)) {
                $rawBody = ['raw' => $response->body()];
            }

            $rawPreview = '';
            try {
                $rawPreview = mb_strimwidth(json_encode($rawBody), 0, 200, '...');
            } catch (\Throwable $e) {
                $rawPreview = '';
            }

            $fallbackMessage = 'SurePass error (HTTP ' . $statusCode . ')';
            if ($rawPreview) {
                $fallbackMessage .= ': ' . $rawPreview;
            }

            return response()->json([
                'status' => 'error',
                'message' => $body['message'] ?? $fallbackMessage,
                'provider_status' => $rawBody,
            ], $statusCode >= 400 ? $statusCode : 500);
        } catch (\Throwable $e) {
            \Log::error('KYC verification via SurePass failed', [
                'organization_id' => $data['organization_id'],
                'document_type' => $data['kyc_document_type'],
                'input_mode' => $data['kyc_input_mode'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'KYC verification failed due to a technical error. Please try again later.',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Consent submission started', [
                'organization_id' => $request->input('organization_id'),
                'phone' => $request->input('phone'),
                'has_otp' => $request->has('otp_code'),
            ]);

            if ($request->has('client_latitude') && $request->input('client_latitude') === '') {
                $request->merge(['client_latitude' => null]);
            }

            if ($request->has('client_longitude') && $request->input('client_longitude') === '') {
                $request->merge(['client_longitude' => null]);
            }

            if ($request->input('signature_method', 'draw') === 'draw') {
                $request->merge(['signature_upload' => null, 'signature_cropped_data' => null]);
            } else {
                $request->merge(['signature_canvas_data' => null]);
            }

            $organization = null;

            $termsAttributes = [
                'en' => 'terms_text',
                'hi' => 'terms_text_hi',
                'mr' => 'terms_text_mr',
            ];

            $liabilityAttributes = [
                'en' => 'liability_release_text',
                'hi' => 'liability_release_text_hi',
                'mr' => 'liability_release_text_mr',
            ];

            $riskAcknowledgementAttributes = [
                'en' => 'risk_acknowledgement_text',
                'hi' => 'risk_acknowledgement_text_hi',
                'mr' => 'risk_acknowledgement_text_mr',
            ];

            $baseRules = [
                'organization_id' => ['required', 'integer', 'exists:organizations,id'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['required', 'string', 'max:20'],
                'otp_code' => ['required', 'string', 'digits:6'],
                'age' => ['nullable', 'integer', 'between:0,150'],
                'sex' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:500'],
                'registration_number' => ['nullable', 'string', 'max:100'],
                'notes' => ['nullable', 'string'],
                'lang' => ['nullable', 'in:en,hi,mr'],
                'consent_for' => ['required', 'in:self,relative'],
                'relative_name' => ['nullable', 'string', 'max:255', 'required_if:consent_for,relative'],
                'relative_relation' => ['nullable', 'string', 'max:255', 'required_if:consent_for,relative'],
                'client_ip' => ['nullable', 'ip'],
                'client_location' => ['required', 'string', 'max:255'],
                'client_latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'client_longitude' => ['nullable', 'numeric', 'between:-180,180'],
                'signature_method' => ['required', 'in:draw,upload'],
                'signature_canvas_data' => ['nullable', 'string', 'required_if:signature_method,draw'],
                'signature_cropped_data' => ['nullable', 'string'],
                'signature_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048', 'required_if:signature_method,upload'],
                'thumb_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
                'thumb_cropped_data' => ['nullable', 'string'],
                'selfie_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:10240'],
                'id_proof_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
                'id_proof_cropped_data' => ['nullable', 'string'],
                'thumb_video_upload' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:10240'],
                'witness_signature_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
                'witness_signature_cropped_data' => ['nullable', 'string'],
                'witness_name' => ['nullable', 'string', 'max:255'],
                'witness_phone' => ['nullable', 'string', 'max:20'],
                'witness_email' => ['nullable', 'email', 'max:255'],
                'witness_relation' => ['nullable', 'string', 'max:255'],
                'risk_consent_text' => ['nullable', 'string'],
                'risk_consent_text_en' => ['nullable', 'string'],
                'risk_consent_text_hi' => ['nullable', 'string'],
                'risk_consent_text_mr' => ['nullable', 'string'],
                'legal_acknowledged' => ['nullable'],
                'risk_acknowledgement_text' => ['nullable', 'string'],
                'risk_acknowledgement_text_hi' => ['nullable', 'string'],
                'risk_acknowledgement_text_mr' => ['nullable', 'string'],
                'kyc_document_type' => ['nullable', 'string', 'max:50'],
                'kyc_document_number' => ['nullable', 'string', 'max:100'],
                'kyc_input_mode' => ['nullable', 'in:number,image'],
            ];

            $formLang = strtolower($request->input('lang', 'en'));
            if (!in_array($formLang, ['en', 'hi', 'mr'], true)) {
                $formLang = 'en';
            }

            $validator = Validator::make($request->all(), $baseRules, [], [
                'legal_acknowledged' => 'Legal acknowledgement',
            ]);

            $validator->after(function ($validator) use ($request) {
                $otpData = session('consent_otp');
                $phone = $request->input('phone');
                $otpCode = $request->input('otp_code');

                if (!$otpData || !is_array($otpData)) {
                    $validator->errors()->add('otp_code', 'Invalid or expired OTP. Please verify your mobile number again.');
                    return;
                }

                if (!isset($otpData['phone'], $otpData['code'], $otpData['expires_at'])) {
                    $validator->errors()->add('otp_code', 'Invalid or expired OTP. Please verify your mobile number again.');
                    return;
                }

                if ($otpData['phone'] !== $phone || $otpData['code'] !== $otpCode) {
                    $validator->errors()->add('otp_code', 'Invalid OTP. Please try again.');
                    return;
                }

                if (($otpData['expires_at'] ?? 0) < time()) {
                    $validator->errors()->add('otp_code', 'OTP has expired. Please request a new one.');
                }
            });

            $organizationId = $request->input('organization_id');
            if ($organizationId) {
                $organization = Organization::find($organizationId);
            }

            $buildLegalTexts = function ($org) use ($termsAttributes, $liabilityAttributes, $riskAcknowledgementAttributes) {
                $terms = [];
                foreach ($termsAttributes as $code => $attribute) {
                    $terms[$code] = trim((string) ($org?->{$attribute} ?? ''));
                }

                $liability = [];
                foreach ($liabilityAttributes as $code => $attribute) {
                    $liability[$code] = trim((string) ($org?->{$attribute} ?? ''));
                }

                $risk = [];
                foreach ($riskAcknowledgementAttributes as $code => $attribute) {
                    $risk[$code] = trim((string) ($org?->{$attribute} ?? ''));
                }

                return [$terms, $liability, $risk];
            };

            [$termsTextByLang, $liabilityTextByLang, $riskAcknowledgementTextByLang] = $buildLegalTexts($organization);

            $langPreferenceOrder = array_values(array_unique([$formLang, 'en', 'hi', 'mr']));

            $primaryTermsText = null;
            foreach ($langPreferenceOrder as $candidateLang) {
                if (!empty($termsTextByLang[$candidateLang])) {
                    $primaryTermsText = $termsTextByLang[$candidateLang];
                    break;
                }
            }

            $primaryLiabilityText = null;
            foreach ($langPreferenceOrder as $candidateLang) {
                if (!empty($liabilityTextByLang[$candidateLang])) {
                    $primaryLiabilityText = $liabilityTextByLang[$candidateLang];
                    break;
                }
            }

            $primaryRiskAcknowledgementText = null;
            foreach ($langPreferenceOrder as $candidateLang) {
                if (!empty($riskAcknowledgementTextByLang[$candidateLang])) {
                    $primaryRiskAcknowledgementText = $riskAcknowledgementTextByLang[$candidateLang];
                    break;
                }
            }

            $hasAnyLegalText = (bool) $primaryTermsText || (bool) $primaryLiabilityText || (bool) $primaryRiskAcknowledgementText;

            if ($hasAnyLegalText) {
                $validator->sometimes('legal_acknowledged', 'accepted', fn () => true);
            }

            $data = $validator->validate();

            // Record OTP verification time as the current time at submission,
            // to avoid timezone mismatches from reconstructing a timestamp.
            $otpVerifiedAt = now();

            session()->forget('consent_otp');
            session()->forget('consent_otp_verified');

            $organization = $organization
                ? $organization->fresh()
                : Organization::findOrFail($data['organization_id']);

            [$termsTextByLang, $liabilityTextByLang, $riskAcknowledgementTextByLang] = $buildLegalTexts($organization);

            $primaryTermsText = null;
            foreach ($langPreferenceOrder as $candidateLang) {
                if (!empty($termsTextByLang[$candidateLang])) {
                    $primaryTermsText = $termsTextByLang[$candidateLang];
                    break;
                }
            }

            $primaryLiabilityText = null;
            foreach ($langPreferenceOrder as $candidateLang) {
                if (!empty($liabilityTextByLang[$candidateLang])) {
                    $primaryLiabilityText = $liabilityTextByLang[$candidateLang];
                    break;
                }
            }

            $primaryRiskAcknowledgementText = null;
            foreach ($langPreferenceOrder as $candidateLang) {
                if (!empty($riskAcknowledgementTextByLang[$candidateLang])) {
                    $primaryRiskAcknowledgementText = $riskAcknowledgementTextByLang[$candidateLang];
                    break;
                }
            }

            $hasAnyLegalText = (bool) $primaryTermsText || (bool) $primaryLiabilityText || (bool) $primaryRiskAcknowledgementText;

            // Log risk consent text for debugging
            $riskText = $request->risk_consent_text;
            if ($riskText) {
                \Log::info('Risk consent text received (length: '.strlen($riskText).')');
            } else {
                \Log::info('Risk consent text is not present in request');
            }

            // Start a database transaction
            \DB::beginTransaction();

            $consentData = [
                'organization_id' => $data['organization_id'],
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'otp_code' => $data['otp_code'],
                'otp_verified_at' => $otpVerifiedAt,
                'status' => 'pending',
                'lang' => $data['lang'] ?? 'en',
                'consent_for' => $data['consent_for'],
                'relative_name' => $data['consent_for'] === 'relative' ? $data['relative_name'] : null,
                'relative_relation' => $data['consent_for'] === 'relative' ? $data['relative_relation'] : null,
                'age' => $data['age'] ?? null,
                'sex' => $data['sex'] ?? null,
                'address' => $data['address'] ?? null,
                'registration_number' => $data['registration_number'] ?? null,
                'client_ip' => $data['client_ip'] ?? $request->ip(),
                'client_location' => $data['client_location'] ?? null,
                'client_latitude' => $data['client_latitude'] ?? null,
                'client_longitude' => $data['client_longitude'] ?? null,
                'signature_method' => $data['signature_method'],
                'witness_name' => $data['witness_name'] ?? null,
                'witness_phone' => $data['witness_phone'] ?? null,
                'witness_email' => $data['witness_email'] ?? null,
                'witness_relation' => $data['witness_relation'] ?? null,
                'kyc_type' => $data['kyc_document_type'] ?? null,
                'kyc_number' => $data['kyc_document_number'] ?? null,
                'kyc_verification_method' => $data['kyc_input_mode'] ?? null,
                'kyc_verified_at' => ($data['kyc_document_type'] ?? null) ? now() : null,
                'terms_acknowledged' => $hasAnyLegalText && !empty($data['legal_acknowledged']),
                'liability_release_acknowledged' => $hasAnyLegalText && !empty($data['legal_acknowledged']),
                'risk_acknowledged' => $hasAnyLegalText && !empty($data['legal_acknowledged']),
            ];

            if ($data['signature_method'] === 'upload') {
                if ($request->hasFile('signature_upload')) {
                    $uploadedFile = $request->file('signature_upload');
                    $path = $uploadedFile->store('signatures', 'public');
                    $consentData['signature_upload_path'] = $path;
                } elseif (!empty($data['signature_cropped_data'])) {
                    $dataUrl = $data['signature_cropped_data'];
                    if (!Str::startsWith($dataUrl, 'data:image/')) {
                        throw new \RuntimeException('Invalid cropped signature image data.');
                    }
                    $parts = explode(',', $dataUrl, 2);
                    if (count($parts) !== 2) {
                        throw new \RuntimeException('Malformed cropped signature image data.');
                    }
                    [$meta, $content] = $parts;
                    $extension = 'png';
                    if (Str::contains($meta, ['image/jpeg', 'image/jpg'])) {
                        $extension = 'jpg';
                    }
                    $binaryData = base64_decode($content);
                    if ($binaryData === false) {
                        throw new \RuntimeException('Invalid cropped signature image payload.');
                    }
                    $fileName = 'signatures/' . uniqid('sig_', true) . '.' . $extension;
                    \Storage::disk('public')->put($fileName, $binaryData);
                    $consentData['signature_upload_path'] = $fileName;
                }
            } elseif ($data['signature_method'] === 'draw' && !empty($data['signature_canvas_data'])) {
                $dataUrl = $data['signature_canvas_data'];
                if (Str::startsWith($dataUrl, 'data:image/')) {
                    $parts = explode(',', $dataUrl, 2);
                    if (count($parts) !== 2) {
                        throw new \RuntimeException('Malformed signature image data.');
                    }
                    [$meta, $content] = $parts;
                    $extension = 'png';
                    if (Str::contains($meta, ['image/jpeg', 'image/jpg'])) {
                        $extension = 'jpg';
                    }
                    $binaryData = base64_decode($content);
                    if ($binaryData === false) {
                        throw new \RuntimeException('Invalid signature image data.');
                    }
                    $fileName = 'signatures/' . uniqid('sig_', true) . '.' . $extension;
                    \Storage::disk('public')->put($fileName, $binaryData);
                    $consentData['signature_upload_path'] = $fileName;
                }
            }

            if ($request->hasFile('thumb_upload')) {
                $thumbPath = $request->file('thumb_upload')->store('thumb-impressions', 'public');
                $consentData['thumb_upload_path'] = $thumbPath;
            } elseif (!empty($data['thumb_cropped_data'])) {
                $thumbDataUrl = $data['thumb_cropped_data'];
                if (!Str::startsWith($thumbDataUrl, 'data:image/')) {
                    throw new \RuntimeException('Invalid cropped thumb image data.');
                }
                $thumbParts = explode(',', $thumbDataUrl, 2);
                if (count($thumbParts) !== 2) {
                    throw new \RuntimeException('Malformed cropped thumb image data.');
                }
                [$thumbMeta, $thumbContent] = $thumbParts;
                $thumbExtension = Str::contains($thumbMeta, ['image/jpeg', 'image/jpg']) ? 'jpg' : 'png';
                $thumbBinary = base64_decode($thumbContent);
                if ($thumbBinary === false) {
                    throw new \RuntimeException('Invalid cropped thumb image payload.');
                }
                $thumbFile = 'thumb-impressions/' . uniqid('thumb_', true) . '.' . $thumbExtension;
                \Storage::disk('public')->put($thumbFile, $thumbBinary);
                $consentData['thumb_upload_path'] = $thumbFile;
            }

            if ($request->hasFile('selfie_upload')) {
                $selfiePath = $request->file('selfie_upload')->store('selfies', 'public');
                $consentData['selfie_upload_path'] = $selfiePath;
            }

            if ($request->hasFile('id_proof_upload')) {
                $idProofPath = $request->file('id_proof_upload')->store('id-proofs', 'public');
                $consentData['id_proof_upload_path'] = $idProofPath;
            } elseif (!empty($data['id_proof_cropped_data'])) {
                $idProofDataUrl = $data['id_proof_cropped_data'];
                if (!Str::startsWith($idProofDataUrl, 'data:image/')) {
                    throw new \RuntimeException('Invalid cropped ID proof image data.');
                }
                $idProofParts = explode(',', $idProofDataUrl, 2);
                if (count($idProofParts) !== 2) {
                    throw new \RuntimeException('Malformed cropped ID proof image data.');
                }
                [$idProofMeta, $idProofContent] = $idProofParts;
                $idProofExtension = Str::contains($idProofMeta, ['image/jpeg', 'image/jpg']) ? 'jpg' : 'png';
                $idProofBinary = base64_decode($idProofContent);
                if ($idProofBinary === false) {
                    throw new \RuntimeException('Invalid cropped ID proof image payload.');
                }
                $idProofFile = 'id-proofs/' . uniqid('id_proof_', true) . '.' . $idProofExtension;
                \Storage::disk('public')->put($idProofFile, $idProofBinary);
                $consentData['id_proof_upload_path'] = $idProofFile;
            }

            if ($request->hasFile('thumb_video_upload')) {
                $thumbVideoPath = $request->file('thumb_video_upload')->store('thumb-videos', 'public');
                $consentData['thumb_video_path'] = $thumbVideoPath;
            }

            if ($request->hasFile('witness_signature_upload')) {
                $witnessPath = $request->file('witness_signature_upload')->store('witness-signatures', 'public');
                $consentData['witness_signature_path'] = $witnessPath;
            } elseif (!empty($data['witness_signature_cropped_data'])) {
                $witnessDataUrl = $data['witness_signature_cropped_data'];
                if (!Str::startsWith($witnessDataUrl, 'data:image/')) {
                    throw new \RuntimeException('Invalid cropped witness signature image data.');
                }
                $witnessParts = explode(',', $witnessDataUrl, 2);
                if (count($witnessParts) !== 2) {
                    throw new \RuntimeException('Malformed cropped witness signature image data.');
                }
                [$witnessMeta, $witnessContent] = $witnessParts;
                $witnessExtension = Str::contains($witnessMeta, ['image/jpeg', 'image/jpg']) ? 'jpg' : 'png';
                $witnessBinary = base64_decode($witnessContent);
                if ($witnessBinary === false) {
                    throw new \RuntimeException('Invalid cropped witness signature image payload.');
                }
                $witnessFile = 'witness-signatures/' . uniqid('witness_', true) . '.' . $witnessExtension;
                \Storage::disk('public')->put($witnessFile, $witnessBinary);
                $consentData['witness_signature_path'] = $witnessFile;
            }

            // Store plain notes (do not mix with risk consent text)
            if (!empty($data['notes'])) {
                $consentData['notes'] = trim($data['notes']);
            }

            $supportedRiskLangs = ['en', 'hi', 'mr'];
            $formLang = $data['lang'] ?? 'en';
            if (!in_array($formLang, $supportedRiskLangs, true)) {
                $formLang = 'en';
            }

            $riskTexts = [
                'en' => isset($data['risk_consent_text_en']) ? trim($data['risk_consent_text_en']) : '',
                'hi' => isset($data['risk_consent_text_hi']) ? trim($data['risk_consent_text_hi']) : '',
                'mr' => isset($data['risk_consent_text_mr']) ? trim($data['risk_consent_text_mr']) : '',
            ];

            $riskTexts = array_map(function ($value) {
                return $value === '' ? null : $value;
            }, $riskTexts);

            $consentData['risk_consent_text_en'] = $riskTexts['en'];
            $consentData['risk_consent_text_hi'] = $formLang === 'hi' ? $riskTexts['hi'] : null;
            $consentData['risk_consent_text_mr'] = $formLang === 'mr' ? $riskTexts['mr'] : null;

            $primaryRiskText = $riskTexts[$formLang] ?? null;
            if (!$primaryRiskText) {
                $primaryRiskText = $riskTexts['en'] ?? null;
            }
            if ($primaryRiskText) {
                $consentData['risk_consent_text'] = $primaryRiskText;
            }

            $consentData['terms_text_en'] = $termsTextByLang['en'] ?: null;
            $consentData['terms_text_hi'] = $termsTextByLang['hi'] ?: null;
            $consentData['terms_text_mr'] = $termsTextByLang['mr'] ?: null;

            $consentData['liability_release_text_en'] = $liabilityTextByLang['en'] ?: null;
            $consentData['liability_release_text_hi'] = $liabilityTextByLang['hi'] ?: null;
            $consentData['liability_release_text_mr'] = $liabilityTextByLang['mr'] ?: null;

            $consentData['terms_text'] = $primaryTermsText ?: null;
            $consentData['liability_release_text'] = $primaryLiabilityText ?: null;

            $consentData['risk_acknowledgement_text_en'] = $riskAcknowledgementTextByLang['en'] ?: null;
            $consentData['risk_acknowledgement_text_hi'] = $riskAcknowledgementTextByLang['hi'] ?: null;
            $consentData['risk_acknowledgement_text_mr'] = $riskAcknowledgementTextByLang['mr'] ?: null;
            $consentData['risk_acknowledgement_text'] = $primaryRiskAcknowledgementText ?: null;

            $consentData['risk_acknowledged'] = $primaryRiskAcknowledgementText ? (bool) ($data['risk_acknowledged'] ?? false) : false;

            $consentData['terms_acknowledged'] = $primaryTermsText ? (bool) ($data['terms_acknowledged'] ?? false) : false;
            $consentData['liability_release_acknowledged'] = $primaryLiabilityText ? (bool) ($data['liability_release_acknowledged'] ?? false) : false;

            // Create the consent record
            Consent::create($consentData);

            // Commit the transaction
            \DB::commit();

            // Ensure organization is available for redirect
            if (!$organization) {
                \Log::warning('Organization not found, fetching from request', [
                    'organization_id' => $request->input('organization_id')
                ]);
                $organization = Organization::findOrFail($request->input('organization_id'));
            }

            \Log::info('Redirecting to submitted page', [
                'organization_id' => $organization->id,
                'route' => 'consent.submitted'
            ]);

            return redirect()->route('consent.submitted', ['organization' => $organization->id])
                ->with('success', 'Consent submitted successfully.');
         } catch (\Exception $e) {
            // Rollback the transaction in case of error
            \DB::rollBack();
            
            // Log the error for debugging
            \Log::error('Error submitting consent: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return back()->with('error', 'An error occurred while submitting the consent. Please try again.');
        }
    }

    public function submitted($organization)
    {
        // Try to find the organization, handle case where it might not exist
        $org = Organization::find($organization);
        
        return view('public.consent.submitted', [
            'organization' => $org,
        ]);
    }
}
