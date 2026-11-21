@php
    $shouldShowForm = session()->has('success') || $errors->any() || old('name') || old('email') || old('phone') || old('notes') || old('relative_name') || old('relative_relation') || old('witness_name') || old('witness_phone') || old('witness_relation') || old('witness_email');
    $consentFor = old('consent_for', 'self');

    $formStep = old('form_step', 'details');
    $detailFields = [
        'name',
        'email',
        'phone',
        'age',
        'sex',
        'address',
        'registration_number',
        'relative_name',
        'relative_relation',
        'notes',
        'signature_method',
        'signature_canvas_data',
        'signature_upload',
        'signature_cropped_data',
        'thumb_upload',
        'thumb_cropped_data',
        'consent_for',
    ];
    $witnessFields = [
        'witness_name',
        'witness_relation',
        'witness_phone',
        'witness_email',
        'witness_signature_upload',
        'witness_signature_cropped_data',
        'risk_acknowledged',
    ];

    $detailErrorsPresent = false;
    foreach ($detailFields as $field) {
        if ($errors->has($field)) {
            $detailErrorsPresent = true;
            break;
        }
    }

    $witnessErrorsPresent = false;
    foreach ($witnessFields as $field) {
        if ($errors->has($field)) {
            $witnessErrorsPresent = true;
            break;
        }
    }

    if ($detailErrorsPresent) {
        $formStep = 'details';
    } elseif ($witnessErrorsPresent) {
        $formStep = 'witness';
    } elseif ($formStep !== 'witness') {
        foreach ($witnessFields as $field) {
            if (old($field)) {
                $formStep = 'witness';
                break;
            }
        }
    }

    $fallbackTranslations = [
        'age' => 'Age',
        'age_placeholder' => 'Enter age',
        'sex' => 'Sex',
        'sex_placeholder' => 'Select sex',
        'address' => 'Address',
        'address_placeholder' => 'Enter full address',
        'registration_number' => 'Registration No.',
        'registration_number_placeholder' => 'Enter registration number',
        'consent_history_title' => 'Consent Details',
        'consent_history_empty' => 'No consents have been submitted yet.',
        'consent_for_label' => 'Consent for',
        'status_label' => 'Status',
        'submitted_at' => 'Submitted on',
        'risk_consent_title' => 'Risk Consent',
        'risk_consent_description' => 'Please review the consent content before providing witness details.',
        'risk_consent_empty' => 'No risk consent content is available yet.',
        'risk_consent_default_heading' => 'Risk Consent Notice',
        'risk_consent_filter_title' => 'Select risk consent categories',
        'risk_consent_filter_help' => 'Choose one or more categories to view the relevant consent content.',
        'risk_consent_none_selected' => 'Select at least one category to view risk consent content.',
        'risk_consent_no_match' => 'No risk consent content available for the selected categories.',
        'risk_consent_uncategorized' => 'Uncategorized',
        'witness_details_title' => 'Witness Details',
        'witness_details_body' => 'Provide the witness information for reference.',
        'witness_name_label' => 'Witness Name',
        'witness_name_placeholder' => 'Enter witness name',
        'witness_phone_label' => 'Witness Phone',
        'witness_phone_placeholder' => 'Enter witness phone number',
        'witness_relation_label' => 'Witness Relation',
        'witness_relation_placeholder' => 'Enter witness relation',
        'witness_email_label' => 'Witness Email',
        'witness_email_placeholder' => 'Enter witness email',
        'verify_phone' => 'Send OTP',
        'enter_otp' => 'Enter OTP',
        'otp_placeholder' => 'Enter OTP received on your mobile number',
    ];

    foreach ($fallbackTranslations as $key => $value) {
        if (!array_key_exists($key, $t ?? [])) {
            $t[$key] = $value;
        }
    }

    if (empty($t['sex_options']) || !is_array($t['sex_options'])) {
        $t['sex_options'] = [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
            'prefer_not_to_say' => 'Prefer not to say',
        ];
    }

    $supportedRiskConsentLangs = ['en', 'hi', 'mr'];
    $riskConsentTextsByLang = [
        'en' => [],
        'hi' => [],
        'mr' => [],
    ];
    $serverRiskConsentTextByLang = [
        'en' => '',
        'hi' => '',
        'mr' => '',
    ];
    $riskConsentDataset = [];

    if (isset($riskConsents) && $riskConsents->isNotEmpty()) {
        $fallbackOrder = [
            'en' => ['content_en', 'content_hi', 'content_mr'],
            'hi' => ['content_hi', 'content_en', 'content_mr'],
            'mr' => ['content_mr', 'content_en', 'content_hi'],
        ];

        foreach ($riskConsents as $index => $riskConsent) {
            $title = $riskConsent->title ?: ($t['risk_consent_default_heading'] ?? 'Risk Consent');
            $contents = [];

            foreach ($supportedRiskConsentLangs as $code) {
                $contentValue = '';
                foreach ($fallbackOrder[$code] as $field) {
                    $candidate = $riskConsent->{$field} ?? '';
                    if (filled($candidate)) {
                        $contentValue = trim((string) $candidate);
                        break;
                    }
                }

                $contents[$code] = $contentValue;
                $heading = '# ' . $title;
                $riskConsentTextsByLang[$code][] = $contentValue !== ''
                    ? $heading . "\n" . $contentValue
                    : $heading;
            }

            $riskConsentDataset[] = [
                'id' => (string) $riskConsent->id,
                'title' => $title,
                'category_id' => $riskConsent->category?->id ?? 'uncategorized',
                'contents' => $contents,
            ];
        }

        foreach ($supportedRiskConsentLangs as $code) {
            $serverRiskConsentTextByLang[$code] = implode("\n\n---\n\n", $riskConsentTextsByLang[$code]);
        }
    }

    $activeLang = $lang ?? 'en';
    if (!in_array($activeLang, $supportedRiskConsentLangs, true)) {
        $activeLang = 'en';
    }

    $serverRiskConsentText = $serverRiskConsentTextByLang[$activeLang] ?? '';
@endphp

<!doctype html>
<html lang="{{ $lang ?? 'en' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consent Form - {{ $organization->name ?? 'Organization' }}</title>
    <!-- Block mixed content script -->
    <meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' https:;">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/cropperjs@1.6.1/dist/cropper.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.1/dist/cropper.min.css">
    <script src="https://unpkg.com/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://unpkg.com/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
</head>
<body class="bg-gray-50">
  <div class="max-w-2xl mx-auto px-4 py-6 sm:px-6">
    <div id="languageCard" class="mb-6 bg-white rounded-xl shadow p-5 border border-gray-100 sm:p-6 {{ $shouldShowForm ? 'hidden' : '' }}">
      <div class="flex flex-col gap-3">
        <div class="text-left">
          <h1 class="text-2xl font-semibold">{{ $organization->name }}</h1>
          <p class="text-gray-600 text-sm sm:text-base">{{ $t['title'] }}</p>
        </div>

        <div>
          <p class="text-sm text-gray-500 mb-3">{{ $t['choose_language'] }}:</p>
          <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('consent.form', ['organization' => $organization->id, 'lang' => 'en']) }}" class="px-3 py-1.5 border rounded text-sm transition hover:bg-gray-50 {{ ($lang ?? 'en')==='en' ? 'bg-gray-900 text-white border-gray-900' : '' }}">EN</a>
            <a href="{{ route('consent.form', ['organization' => $organization->id, 'lang' => 'hi']) }}" class="px-3 py-1.5 border rounded text-sm transition hover:bg-gray-50 {{ ($lang ?? 'en')==='hi' ? 'bg-gray-900 text-white border-gray-900' : '' }}">HI</a>
            <a href="{{ route('consent.form', ['organization' => $organization->id, 'lang' => 'mr']) }}" class="px-3 py-1.5 border rounded text-sm transition hover:bg-gray-50 {{ ($lang ?? 'en')==='mr' ? 'bg-gray-900 text-white border-gray-900' : '' }}">MR</a>
          </div>
        </div>

        <button id="languageNext" type="button" class="mt-4 inline-flex items-center justify-center rounded bg-gray-900 px-4 py-2 text-white text-sm font-medium">Next</button>
      </div>
    </div>

    <div id="welcomeCard" class="mb-6 bg-white rounded-xl shadow p-5 border border-gray-100 sm:p-6 hidden">
      <div class="flex flex-col gap-3">
        <div class="text-left">
          <h1 class="text-2xl font-semibold">{{ $organization->name }}</h1>
          <p class="text-gray-600 text-sm sm:text-base">{{ $t['title'] }}</p>
        </div>

        <div class="bg-gray-50 border border-gray-100 rounded-lg p-4">
          <h2 class="text-lg font-semibold text-gray-900">{{ $t['welcome_title'] }}</h2>
          <p class="mt-2 text-sm text-gray-600 leading-relaxed">{{ $t['welcome_body'] }}</p>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:gap-3">
          <button id="welcomeBack" type="button" class="inline-flex items-center justify-center rounded border border-gray-300 px-4 py-2 text-gray-700 text-sm font-medium hover:bg-gray-50">Back</button>
          <button id="welcomeNext" type="button" class="inline-flex items-center justify-center rounded bg-gray-900 px-4 py-2 text-white text-sm font-medium">Next</button>
        </div>
      </div>
    </div>

    <div id="consentForCard" class="mb-6 bg-white rounded-xl shadow p-5 border border-gray-100 sm:p-6 hidden">
      <div class="flex flex-col gap-4">
        <div class="text-left">
          <h2 class="text-xl font-semibold text-gray-900">{{ $t['consent_for_title'] }}</h2>
          <p class="mt-1 text-sm text-gray-600">{{ $t['consent_for_body'] }}</p>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <button type="button" data-consent-option="self" class="consent-option flex flex-col gap-1 rounded-lg border border-gray-200 px-4 py-3 text-left transition hover:border-gray-400 hover:bg-gray-50" aria-pressed="false">
            <span class="text-sm font-semibold text-gray-900">{{ $t['consent_for_self'] }}</span>
            <span class="text-xs text-gray-500">{{ $t['consent_for_self_help'] }}</span>
          </button>
          <button type="button" data-consent-option="relative" class="consent-option flex flex-col gap-1 rounded-lg border border-gray-200 px-4 py-3 text-left transition hover:border-gray-400 hover:bg-gray-50" aria-pressed="false">
            <span class="text-sm font-semibold text-gray-900">{{ $t['consent_for_relative'] }}</span>
            <span class="text-xs text-gray-500">{{ $t['consent_for_relative_help'] }}</span>
          </button>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:gap-3">
          <button id="consentForBack" type="button" class="inline-flex items-center justify-center rounded border border-gray-300 px-4 py-2 text-gray-700 text-sm font-medium hover:bg-gray-50">Back</button>
          <button id="consentForNext" type="button" class="inline-flex items-center justify-center rounded bg-gray-900 px-4 py-2 text-white text-sm font-medium">Next</button>
        </div>
      </div>
    </div>

    <div id="identityCard" class="mb-6 bg-white rounded-xl shadow p-5 border border-gray-100 sm:p-6 hidden">
      <div class="flex flex-col gap-4">
        <div class="text-left">
          <h2 class="text-xl font-semibold text-gray-900">Upload Selfie & ID Proof</h2>
          <p class="mt-1 text-sm text-gray-600">Please upload a recent selfie and a clear photo of your ID proof for verification.</p>
        </div>

        <div class="space-y-4">
          <div class="space-y-1">
            <label class="block text-sm text-gray-700" for="selfieUploadInput">Selfie (optional)</label>
            <input id="selfieUploadInput" type="file" name="selfie_upload" accept="image/*" capture="user" form="consentForm" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-700">
            <p class="text-xs text-gray-500">Upload a recent selfie (max 2 MB).</p>
            @error('selfie_upload')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
          </div>

          <div class="space-y-1">
            <label class="block text-sm text-gray-700" for="idProofUploadInput">ID Proof (optional)</label>
            <input id="idProofUploadInput" type="file" name="id_proof_upload" accept="image/*" capture="environment" form="consentForm" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-700">
            <p class="text-xs text-gray-500">Upload a clear image of your ID proof (max 2 MB).</p>
            <input type="hidden" name="id_proof_cropped_data" id="idProofCroppedData" form="consentForm" value="{{ old('id_proof_cropped_data') }}">
            <div id="idProofUploadPreview" class="hidden mt-3 rounded border border-gray-200 bg-gray-50 p-3" style="display:none !important;">
              <div class="flex items-center justify-between text-xs text-gray-600">
                <span>Cropped preview</span>
                <button type="button" id="idProofReCropButton" class="inline-flex items-center gap-1 rounded border border-gray-300 px-2 py-1 font-medium text-gray-700 hover:bg-gray-100">
                  Re-crop
                </button>
              </div>
              <div class="mt-2 overflow-hidden rounded border border-gray-200 bg-white">
                <img id="idProofUploadPreviewImage" src="" alt="ID proof preview" class="block max-h-60 w-full object-contain bg-white">
              </div>
            </div>
            <div id="idProofUploadError" class="hidden text-xs text-rose-600 mt-2">Please crop ID proof image before submitting.</div>
            @error('id_proof_upload')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
            @error('id_proof_cropped_data')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:gap-3">
          <button id="identityBack" type="button" class="inline-flex items-center justify-center rounded border border-gray-300 px-4 py-2 text-gray-700 text-sm font-medium hover:bg-gray-50">Back</button>
          <button id="identityNext" type="button" class="inline-flex items-center justify-center rounded bg-gray-900 px-4 py-2 text-white text-sm font-medium">Next</button>
        </div>
      </div>
    </div>

    <div id="formSection" class="{{ $shouldShowForm ? '' : 'hidden' }}">
      <div class="mb-6 text-left">
        <h1 class="text-2xl font-semibold">{{ $organization->name }}</h1>
        <p class="text-gray-600 text-sm sm:text-base">{{ $t['title'] }}</p>
      </div>

      @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-100 bg-emerald-50/80 p-4 sm:p-5 flex items-start gap-3">
          <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293A1 1 0 106.293 10.707l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="text-sm text-emerald-800">
            <h2 class="font-semibold">Consent submitted successfully</h2>
            <p class="mt-1">{{ session('success') }}</p>
          </div>
        </div>
        
      @endif

      <form id="consentForm" method="POST" action="{{ url('/consent') }}" enctype="multipart/form-data" class="space-y-4 bg-white p-4 rounded-xl shadow sm:p-6">
        @csrf
        <input type="hidden" name="organization_id" value="{{ $organization->id }}">
        <input type="hidden" name="lang" value="{{ $lang ?? 'en' }}">
        <input type="hidden" name="consent_for" id="consentForInput" value="{{ $consentFor }}">
        <input type="hidden" name="form_step" id="formStepInput" value="{{ $formStep }}">
        <input type="hidden" name="client_ip" id="clientIpInput" value="{{ request()->ip() }}">
        <input type="hidden" name="client_location" id="clientLocationInput" value="">
        <input type="hidden" name="client_latitude" id="clientLatitudeInput" value="">
        <input type="hidden" name="client_longitude" id="clientLongitudeInput" value="">
        <textarea name="risk_consent_text" id="riskConsentTextInput" class="hidden" hidden>{{ old('risk_consent_text', $serverRiskConsentTextByLang[$activeLang] ?? '') }}</textarea>
        <textarea name="risk_consent_text_en" id="riskConsentTextInputEn" class="hidden" hidden>{{ old('risk_consent_text_en', $serverRiskConsentTextByLang['en'] ?? '') }}</textarea>
        <textarea name="risk_consent_text_hi" id="riskConsentTextInputHi" class="hidden" hidden>{{ old('risk_consent_text_hi', $serverRiskConsentTextByLang['hi'] ?? '') }}</textarea>
        <textarea name="risk_consent_text_mr" id="riskConsentTextInputMr" class="hidden" hidden>{{ old('risk_consent_text_mr', $serverRiskConsentTextByLang['mr'] ?? '') }}</textarea>

        <div id="primaryDetailsSection" class="space-y-4 {{ $formStep === 'witness' ? 'hidden' : '' }}">
          <div>
            <label class="block text-sm text-gray-700">{{ $t['name'] }} <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 w-full border rounded px-3 py-2" placeholder="{{ $t['name'] }}">
            @error('name')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="block text-sm text-gray-700">{{ $t['email'] }}</label>
              <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="{{ $t['email'] }}">
              @error('email')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">{{ $t['phone'] }} <span class="text-rose-500">*</span></label>
              <div class="mt-1 flex gap-2">
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2 flex-1" placeholder="{{ $t['phone'] }}">
                <button
                  type="button"
                  id="sendOtpButton"
                  data-send-otp-url="{{ route('consent.send_otp') }}"
                  class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50"
                >
                  {{ $t['verify_phone'] ?? 'Verify' }}
                </button>
              </div>
              @error('phone')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
              <div class="mt-2">
                <label class="block text-xs text-gray-700">{{ $t['enter_otp'] ?? 'Enter OTP' }}</label>
                <div class="mt-1 flex gap-2">
                  <input
                    type="text"
                    name="otp_code"
                    id="otp_code"
                    value="{{ old('otp_code') }}"
                    class="w-full border rounded px-3 py-2 text-sm flex-1"
                    placeholder="{{ $t['otp_placeholder'] ?? 'Enter OTP received on your mobile number' }}"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                  >
                  <button
                    type="button"
                    id="verifyOtpButton"
                    data-verify-otp-url="{{ route('consent.verify_otp') }}"
                    class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50"
                  >
                    Verify OTP
                  </button>
                </div>
                @error('otp_code')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
                <p id="otpStatus" class="mt-1 text-xs"></p>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="block text-sm text-gray-700">{{ $t['age'] }} <span class="text-rose-500">*</span></label>
              <input type="number" name="age" value="{{ old('age') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="{{ $t['age_placeholder'] }}" min="0" max="150" required>
              @error('age')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">{{ $t['sex'] }} <span class="text-rose-500">*</span></label>
              <select name="sex" class="mt-1 w-full border rounded px-3 py-2" required>
                <option value="">{{ $t['sex_placeholder'] }}</option>
                @foreach ($t['sex_options'] as $value => $label)
                  <option value="{{ $value }}" {{ old('sex') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
              @error('sex')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          <div>
            <label class="block text-sm text-gray-700">{{ $t['address'] }} <span class="text-rose-500">*</span></label>
            <textarea name="address" class="mt-1 w-full border rounded px-3 py-2" rows="3" placeholder="{{ $t['address_placeholder'] }}" required>{{ old('address') }}</textarea>
            @error('address')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="block text-sm text-gray-700">{{ $t['registration_number'] }}</label>
            <input type="text" name="registration_number" value="{{ old('registration_number') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="{{ $t['registration_number_placeholder'] }}">
            @error('registration_number')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
          </div>

          <div id="relativeDetails" class="{{ $consentFor === 'relative' ? '' : 'hidden' }} space-y-4 rounded-lg border border-gray-100 bg-gray-50/60 p-4">
            <div>
              <h3 class="text-sm font-semibold text-gray-900">{{ $t['relative_details_title'] }}</h3>
              <p class="mt-1 text-xs text-gray-600">{{ $t['relative_details_body'] }}</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <label class="block text-sm text-gray-700" for="relative_name">{{ $t['relative_name_label'] }} <span class="text-rose-500" aria-hidden="true">*</span></label>
                <input type="text" id="relative_name" name="relative_name" value="{{ old('relative_name') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="{{ $t['relative_name_placeholder'] }}" data-relative-field>
                @error('relative_name')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700" for="relative_relation">{{ $t['relative_relation_label'] }} <span class="text-rose-500" aria-hidden="true">*</span></label>
                <input type="text" id="relative_relation" name="relative_relation" value="{{ old('relative_relation') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="{{ $t['relative_relation_placeholder'] }}" data-relative-field>
                @error('relative_relation')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm text-gray-700">{{ $t['notes'] }}</label>
            <textarea name="notes" class="mt-1 w-full border rounded px-3 py-2" rows="4" placeholder="{{ $t['notes'] }}..."></textarea>
            @error('notes')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
          </div>

          <div class="space-y-3">
            <div>
              <span class="block text-sm text-gray-700">Signature Method <span class="text-rose-500">*</span></span>
              <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                  <input type="radio" name="signature_method" value="draw" {{ old('signature_method', 'upload') === 'draw' ? 'checked' : '' }} class="h-4 w-4 text-gray-900 focus:ring-gray-900">
                  <span>Draw signature on screen</span>
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                  <input type="radio" name="signature_method" value="upload" {{ old('signature_method', 'upload') === 'upload' ? 'checked' : '' }} class="h-4 w-4 text-gray-900 focus:ring-gray-900">
                  <span>Upload signature image</span>
                </label>
              </div>
              @error('signature_method')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div id="signatureDrawGroup" class="space-y-2 {{ old('signature_method', 'upload') === 'draw' ? '' : 'hidden' }}">
              <label class="block text-sm text-gray-700">Draw Signature <span class="text-rose-500">*</span></label>
              <div class="rounded-lg border border-gray-200 bg-white p-3 shadow-sm">
                <div class="relative">
                  <canvas id="signatureCanvas" width="1024" height="360" class="w-full h-60 rounded border border-dashed border-gray-300 bg-gray-50"></canvas>
                  <div id="signatureCanvasPlaceholder" class="pointer-events-none absolute inset-0 flex items-center justify-center text-sm text-gray-400 select-none">
                    Sign here using your finger or stylus
                  </div>
                </div>
                <div class="mt-2 flex items-center justify-end text-xs text-gray-500">
                  <button type="button" id="signatureClearButton" class="inline-flex items-center gap-1 rounded border border-gray-300 px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100">
                    Clear
                  </button>
                </div>
              </div>
              <input type="hidden" name="signature_canvas_data" id="signatureCanvasData" value="{{ old('signature_canvas_data') }}">
              <div id="signatureCanvasError" class="hidden text-xs text-rose-600">Please provide a drawn signature before submitting.</div>
              @error('signature_canvas_data')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div id="signatureUploadGroup" class="space-y-1 {{ old('signature_method', 'upload') === 'upload' ? '' : 'hidden' }}">
              <label class="block text-sm text-gray-700" for="signatureUploadInput">Signature File <span class="text-rose-500">*</span></label>
              <input id="signatureUploadInput" type="file" name="signature_upload" accept="image/*" capture="environment" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-700">
              <p class="text-xs text-gray-500">Upload a clear image of the signed document (max 2 MB).</p>
              <input type="hidden" name="signature_cropped_data" id="signatureCroppedData" value="{{ old('signature_cropped_data') }}">
              <div id="signatureUploadPreview" class="hidden mt-3 rounded border border-gray-200 bg-gray-50 p-3" style="display:none !important;">
                <div class="flex items-center justify-between text-xs text-gray-600">
                  <span>Cropped preview</span>
                  <button type="button" id="signatureReCropButton" class="inline-flex items-center gap-1 rounded border border-gray-300 px-2 py-1 font-medium text-gray-700 hover:bg-gray-100">
                    Re-crop
                  </button>
                </div>
                <div class="mt-2 overflow-hidden rounded border border-gray-200 bg-white">
                  <img id="signatureUploadPreviewImage" src="" alt="Signature preview" class="block max-h-60 w-full object-contain bg-white">
                </div>
              </div>
              <div id="signatureUploadError" class="hidden text-xs text-rose-600 mt-2">Please upload a valid image file before submitting.</div>
              @error('signature_upload')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
              @error('signature_cropped_data')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="space-y-1">
              <label class="block text-sm text-gray-700" for="thumbUploadInput">Thumb Impression (optional)</label>
              <input id="thumbUploadInput" type="file" name="thumb_upload" accept="image/*" capture="environment" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-700">
              <p class="text-xs text-gray-500">Upload a clear photo of thumb impression (max 2 MB)</p>
              <input type="hidden" name="thumb_cropped_data" id="thumbCroppedData" value="{{ old('thumb_cropped_data') }}">
              <div id="thumbUploadPreview" class="hidden mt-3 rounded border border-gray-200 bg-gray-50 p-3" style="display:none !important;">
                <div class="flex items-center justify-between text-xs text-gray-600">
                  <span>Cropped preview</span>
                  <button type="button" id="thumbReCropButton" class="inline-flex items-center gap-1 rounded border border-gray-300 px-2 py-1 font-medium text-gray-700 hover:bg-gray-100">
                    Re-crop
                  </button>
                </div>
                <div class="mt-2 overflow-hidden rounded border border-gray-200 bg-white">
                  <img id="thumbUploadPreviewImage" src="" alt="Thumb preview" class="block max-h-60 w-full object-contain bg-white">
                </div>
              </div>
              <div id="thumbUploadError" class="hidden text-xs text-rose-600 mt-2">Please upload a valid image file before submitting.</div>
              @error('thumb_upload')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
              @error('thumb_cropped_data')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="space-y-1">
              <label class="block text-sm text-gray-700" for="thumbVideoUploadInput">Upload Video (optional)</label>
              <input id="thumbVideoUploadInput" type="file" name="thumb_video_upload" accept="video/*" capture="user" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-700">
              <p class="text-xs text-gray-500">Upload a short video related to the thumb impression if needed (max 10 MB).</p>
              @error('thumb_video_upload')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="pt-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
            <button id="formBack" type="button" class="inline-flex items-center justify-center rounded border border-gray-300 px-4 py-2 text-gray-700 text-sm font-medium hover:bg-gray-50">Back</button>
            <button type="button" id="formNext" class="w-full px-4 py-2 rounded bg-gray-900 text-white sm:w-auto">Next</button>
          </div>
        </div>

        <div id="witnessSection" class="space-y-4 {{ $formStep === 'witness' ? '' : 'hidden' }}">
          <div id="riskPage" class="space-y-4 rounded-lg border border-gray-100 bg-gray-50/60 p-4">
            <div id="kycStep" class="space-y-4">
              <div class="space-y-3 rounded-lg border border-gray-100 bg-white p-4">
                <div>
                  <h3 class="text-sm font-semibold text-gray-900">KYC</h3>
                  <p class="mt-1 text-xs text-gray-600">Please verify the KYC details before proceeding.</p>
                </div>

                <div class="mt-3">
                  <label for="kyc_document_type" class="block text-sm text-gray-700">Select document</label>
                  <select
                    id="kyc_document_type"
                    name="kyc_document_type"
                    class="mt-1 w-full border rounded px-3 py-2 text-sm text-gray-900"
                  >
                    <option value="">Select document</option>
                    <option value="pan" {{ old('kyc_document_type') === 'pan' ? 'selected' : '' }}>PAN Card</option>
                    <option value="voter_id" {{ old('kyc_document_type') === 'voter_id' ? 'selected' : '' }}>Voter ID</option>
                  </select>
                </div>

                <div id="kycInputOptions" class="mt-3 hidden">
                  <div class="space-y-3">
                    <div>
                      <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input
                          type="radio"
                          name="kyc_input_mode"
                          value="number"
                          class="h-4 w-4 text-gray-900 focus:ring-gray-900"
                          {{ old('kyc_input_mode', 'number') === 'number' ? 'checked' : '' }}
                        >
                        <span>Document number</span>
                      </label>
                      <input
                        type="text"
                        id="kyc_document_number"
                        name="kyc_document_number"
                        value="{{ old('kyc_document_number') }}"
                        class="mt-1 w-full border rounded px-3 py-2 text-sm text-gray-900"
                        placeholder="Enter document number"
                      >
                    </div>

                    <div>
                      <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input
                          type="radio"
                          name="kyc_input_mode"
                          value="image"
                          class="h-4 w-4 text-gray-900 focus:ring-gray-900"
                          {{ old('kyc_input_mode') === 'image' ? 'checked' : '' }}
                        >
                        <span>Upload document image</span>
                      </label>
                      <input
                        type="file"
                        id="kyc_document_image"
                        name="kyc_document_image"
                        accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-700"
                      >
                    </div>
                  </div>
                </div>

                <div id="kycVerifyStatus" class="mt-2 text-xs hidden"></div>
              </div>

              <div class="pt-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                <button type="button" id="kycBackButton" class="inline-flex items-center justify-center rounded border border-gray-300 px-4 py-2 text-gray-700 text-sm font-medium hover:bg-gray-50">Back</button>
                <button
                  type="button"
                  id="kycVerifyButton"
                  data-kyc-verify-url="{{ route('consent.kyc_verify') }}"
                  class="w-full px-4 py-2 rounded bg-indigo-600 text-white sm:w-auto"
                >
                  Verify KYC
                </button>
                <button type="button" id="kycNextButton" class="w-full px-4 py-2 rounded bg-gray-900 text-white sm:w-auto">Next</button>
              </div>
            </div>

            <div id="riskConsentStep" class="space-y-4 hidden">
              @if(isset($riskConsentCategories) && $riskConsentCategories->isNotEmpty())
                <div class="space-y-2">
                  <h3 class="text-sm font-semibold text-gray-900">{{ $t['risk_consent_filter_title'] }}</h3>
                  <p class="text-xs text-gray-600">{{ $t['risk_consent_filter_help'] }}</p>

                  <div class="flex flex-wrap gap-2">
                    @foreach ($riskConsentCategories as $index => $category)
                      @php
                        $categoryId = $category['id'] ?? 'uncategorized';
                        $categoryLabel = $category['name'] ?? $t['risk_consent_uncategorized'];
                        $inputId = 'risk-consent-filter-' . $index;
                      @endphp

                      <label for="{{ $inputId }}" class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1 text-xs text-gray-700 shadow-sm transition hover:border-gray-300">
                        <input
                          type="checkbox"
                          id="{{ $inputId }}"
                          value="{{ $categoryId }}"
                          class="h-3.5 w-3.5 rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                          data-risk-consent-filter
                        >
                        <span>{{ $categoryLabel }}</span>
                      </label>
                    @endforeach
                  </div>
                </div>
              @endif

              <div>
                <h3 class="text-sm font-semibold text-gray-900">{{ $t['risk_consent_title'] }}</h3>
                <p class="mt-1 text-xs text-gray-600">{{ $t['risk_consent_description'] }}</p>
              </div>

              @php
                $contentField = 'content_' . ($lang ?? 'en');
              @endphp

              @if(isset($riskConsents) && $riskConsents->isNotEmpty())
                <div id="riskConsentSelectionMessage" class="rounded-lg border border-dashed border-gray-200 bg-white p-4 text-xs text-gray-500">
                  {{ $t['risk_consent_none_selected'] }}
                </div>

                <div id="riskConsentNoMatchMessage" class="hidden rounded-lg border border-dashed border-gray-200 bg-white p-4 text-xs text-gray-500">
                  {{ $t['risk_consent_no_match'] }}
                </div>

                <div class="space-y-3">
                  @foreach ($riskConsents as $riskConsent)
                    @php
                      $localizedContent = $riskConsent->{$contentField} ?? '';

                      if (!filled($localizedContent)) {
                          $localizedContent = $riskConsent->content_en
                              ?: $riskConsent->content_hi
                              ?: $riskConsent->content_mr
                              ?: '';
                      }

                      $cardCategoryId = $riskConsent->category?->id ?? 'uncategorized';
                    @endphp

                    <div
                      class="hidden rounded-xl border border-gray-200 bg-white p-4 shadow-sm"
                      data-risk-consent-card
                      data-category-id="{{ $cardCategoryId }}"
                      data-consent-id="{{ $riskConsent->id }}"
                    >
                      <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between gap-2">
                          <h4 class="text-sm font-semibold text-gray-900">{{ $riskConsent->title ?: $t['risk_consent_default_heading'] }}</h4>
                          @if ($riskConsent->category)
                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-600">{{ $riskConsent->category->name }}</span>
                          @endif
                        </div>

                        @if (filled($localizedContent))
                          <div class="prose prose-sm max-w-none text-gray-700">
                            {!! nl2br(e($localizedContent)) !!}
                          </div>
                        @else
                          <span class="text-xs text-gray-500">{{ $t['risk_consent_empty'] }}</span>
                        @endif
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="rounded-lg border border-dashed border-gray-200 bg-white p-4 text-xs text-gray-500">
                  {{ $t['risk_consent_empty'] }}
                </div>
              @endif

              <div class="pt-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                <button type="button" id="riskBackButton" class="inline-flex items-center justify-center rounded border border-gray-300 px-4 py-2 text-gray-700 text-sm font-medium hover:bg-gray-50">Back</button>
                <button type="button" id="riskNextButton" class="w-full px-4 py-2 rounded bg-gray-900 text-white sm:w-auto">Next</button>
              </div>
            </div>
          </div>

          <div id="legalPage" class="space-y-4 rounded-lg border border-gray-100 bg-gray-50/60 p-4 hidden">
            @php
              $termsLanguageLabel = $termsLangUsed && isset($legalLanguageLabels[$termsLangUsed])
                ? $legalLanguageLabels[$termsLangUsed]
                : null;
              $liabilityLanguageLabel = $liabilityLangUsed && isset($legalLanguageLabels[$liabilityLangUsed])
                ? $legalLanguageLabels[$liabilityLangUsed]
                : null;
              $riskAcknowledgementLanguageLabel = $riskAcknowledgementLangUsed && isset($legalLanguageLabels[$riskAcknowledgementLangUsed])
                ? $legalLanguageLabels[$riskAcknowledgementLangUsed]
                : null;
            @endphp

            @if (!empty($liabilityClauses))
            <div class="space-y-3 rounded-lg border border-gray-100 bg-white p-4">
              <div>
                <h3 class="text-sm font-semibold text-gray-900">{{ $t['liability_title'] }}</h3>
                <p class="mt-1 text-xs text-gray-600">{{ $t['liability_description'] }}</p>
                @if($liabilityLanguageLabel)
                  <p class="text-[11px] text-gray-500 mt-2">Language: {{ $liabilityLanguageLabel }}</p>
                @endif
              </div>
              <ol class="list-decimal space-y-2 pl-5 text-xs text-gray-700">
                @foreach ($liabilityClauses as $clause)
                  <li>{{ $clause }}</li>
                @endforeach
              </ol>
            </div>
            @endif

            @if (!empty($riskAcknowledgementClauses))
            <div class="space-y-3 rounded-lg border border-gray-100 bg-white p-4">
              <div>
                <h3 class="text-sm font-semibold text-gray-900">{{ $t['risk_acknowledgement_title'] }}</h3>
                <p class="mt-1 text-xs text-gray-600">{{ $t['risk_acknowledgement_description'] }}</p>
                @if($riskAcknowledgementLanguageLabel)
                  <p class="text-[11px] text-gray-500 mt-2">Language: {{ $riskAcknowledgementLanguageLabel }}</p>
                @endif
              </div>
              <ol class="list-decimal space-y-2 pl-5 text-xs text-gray-700">
                @foreach ($riskAcknowledgementClauses as $clause)
                  <li>{{ $clause }}</li>
                @endforeach
              </ol>
            </div>
            @endif

            @if (!empty($termsClauses))
            <div id="legalContent" class="space-y-4 hidden">
              <div id="termsSection" class="space-y-3 rounded-lg border border-gray-100 bg-white p-4">
                <div>
                  <h3 class="text-sm font-semibold text-gray-900">{{ $t['terms_title'] }}</h3>
                  <p class="mt-1 text-xs text-gray-600">{{ $t['terms_description'] }}</p>
                  @if($termsLanguageLabel)
                    <p class="text-[11px] text-gray-500 mt-2">Language: {{ $termsLanguageLabel }}</p>
                  @endif
                </div>
                <ol class="list-decimal space-y-2 pl-5 text-xs text-gray-700">
                  @foreach ($termsClauses as $clause)
                    <li>{{ $clause }}</li>
                  @endforeach
                </ol>
              </div>
            </div>
            @endif

            @if (!empty($termsClauses) || !empty($liabilityClauses) || !empty($riskAcknowledgementClauses))
            <div class="space-y-2 rounded-lg border border-gray-100 bg-white p-4">
              <label class="flex items-start gap-2 text-xs text-gray-700">
                <input
                  type="checkbox"
                  name="legal_acknowledged"
                  value="1"
                  class="mt-1 h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                  {{ old('legal_acknowledged') ? 'checked' : '' }}
                  required
                >
                <span>
                  <a
                    href="#termsSection"
                    class="text-indigo-600 underline"
                    onclick="(function(){var content=document.getElementById('legalContent');if(!content){return false;}var isHidden=content.classList.contains('hidden');if(isHidden){content.classList.remove('hidden');content.scrollIntoView({behavior:'smooth',block:'start'});}else{content.classList.add('hidden');}return false;})()"
                  >
                    {{ $t['terms_title'] }}
                  </a>
                </span>
              </label>
              @error('legal_acknowledged')<div class="text-rose-600 text-xs">{{ $message }}</div>@enderror
            </div>
            @endif

            <div class="pt-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
              <button type="button" id="legalBackButton" class="inline-flex items-center justify-center rounded border border-gray-300 px-4 py-2 text-gray-700 text-sm font-medium hover:bg-gray-50">Back</button>
              <button type="button" id="legalNextButton" class="w-full px-4 py-2 rounded bg-gray-900 text-white sm:w-auto">Next</button>
            </div>
          </div>

          <div id="witnessPage" class="space-y-4 rounded-lg border border-gray-100 bg-gray-50/60 p-4 hidden">
            <div>
              <h3 class="text-sm font-semibold text-gray-900">{{ $t['witness_details_title'] }}</h3>
              <p class="mt-1 text-xs text-gray-600">{{ $t['witness_details_body'] }}</p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label class="block text-sm text-gray-700" for="witness_name">{{ $t['witness_name_label'] }}</label>
                <input type="text" id="witness_name" name="witness_name" value="{{ old('witness_name') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="{{ $t['witness_name_placeholder'] }}">
                @error('witness_name')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700" for="witness_relation">{{ $t['witness_relation_label'] }}</label>
                <input type="text" id="witness_relation" name="witness_relation" value="{{ old('witness_relation') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="{{ $t['witness_relation_placeholder'] }}">
                @error('witness_relation')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700" for="witness_phone">{{ $t['witness_phone_label'] }}</label>
                <input type="text" id="witness_phone" name="witness_phone" value="{{ old('witness_phone') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="{{ $t['witness_phone_placeholder'] }}">
                @error('witness_phone')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700" for="witness_email">{{ $t['witness_email_label'] }}</label>
                <input type="email" id="witness_email" name="witness_email" value="{{ old('witness_email') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="{{ $t['witness_email_placeholder'] }}">
                @error('witness_email')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="space-y-1">
              <label class="block text-sm text-gray-700" for="witnessUploadInput">Witness Signature (optional)</label>
              <input id="witnessUploadInput" type="file" name="witness_signature_upload" accept="image/*" capture="environment" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-700">
              <p class="text-xs text-gray-500">Have the witness upload a clear image of their signature (max 2 MB).</p>
              <input type="hidden" name="witness_signature_cropped_data" id="witnessCroppedData" value="{{ old('witness_signature_cropped_data') }}">
              <div id="witnessUploadPreview" class="hidden mt-3 rounded border border-gray-200 bg-gray-50 p-3" style="display:none !important;">
                <div class="flex items-center justify-between text-xs text-gray-600">
                  <span>Cropped preview</span>
                  <button type="button" id="witnessReCropButton" class="inline-flex items-center gap-1 rounded border border-gray-300 px-2 py-1 font-medium text-gray-700 hover:bg-gray-100">
                    Re-crop
                  </button>
                </div>
                <div class="mt-2 overflow-hidden rounded border border-gray-200 bg-white">
                  <img id="witnessUploadPreviewImage" src="" alt="Witness signature preview" class="block max-h-60 w-full object-contain bg-white">
                </div>
              </div>
              <div id="witnessUploadError" class="hidden text-xs text-rose-600 mt-2">Please crop witness signature image before submitting.</div>
              @error('witness_signature_upload')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
              @error('witness_signature_cropped_data')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="pt-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
              <button type="button" id="witnessBackButton" class="inline-flex items-center justify-center rounded border border-gray-300 px-4 py-2 text-gray-700 text-sm font-medium hover:bg-gray-50">Back</button>
              <button class="w-full px-4 py-2 rounded bg-gray-900 text-white sm:w-auto">{{ $t['submit'] }}</button>
            </div>
          </div>
        </div>
      </form>

      <div class="text-center text-xs text-gray-500 mt-6">We adhere to the provisions of the Digital Personal Data Protection Act - 2023.</div>
    </div>
  </div>

  <script type="application/json" id="riskConsentDataset">{!! json_encode($riskConsentDataset, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
  <script type="application/json" id="riskConsentConfig">{!! json_encode([
    'activeLang' => $activeLang,
    'supportedLangs' => $supportedRiskConsentLangs,
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

  <div id="signatureCropModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-gray-900/70"></div>
    <div class="relative mx-auto w-full max-w-3xl rounded-xl bg-white p-4 shadow-lg sm:p-6">
      <div class="flex items-start justify-between">
        <div>
          <h2 class="text-lg font-semibold text-gray-900">Crop Signature</h2>
          <p class="text-sm text-gray-500">Drag the handles to crop the signature image, then apply.</p>
        </div>
        <button type="button" id="signatureCropClose" class="rounded-full p-1 text-gray-500 hover:bg-gray-100">
          <span class="sr-only">Close</span>
          <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M10 8.586 4.293 2.879 2.879 4.293 8.586 10l-5.707 5.707 1.414 1.414L10 11.414l5.707 5.707 1.414-1.414L11.414 10l5.707-5.707-1.414-1.414L10 8.586Z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
      <div class="mt-4 max-h-[60vh] overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
        <img id="signatureCropImage" src="" alt="Signature to crop" class="block max-h-[60vh] w-full object-contain">
      </div>
      <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
        <button type="button" id="signatureCropCancel" class="inline-flex items-center justify-center rounded border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
        <button type="button" id="signatureCropConfirm" class="inline-flex items-center justify-center rounded bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">Apply Crop</button>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const shouldShowForm = {{ $shouldShowForm ? 'true' : 'false' }};
      const languageCard = document.getElementById('languageCard');
      const welcomeCard = document.getElementById('welcomeCard');
      const consentForCard = document.getElementById('consentForCard');
      const formSection = document.getElementById('formSection');
      const languageNext = document.getElementById('languageNext');
      const welcomeNext = document.getElementById('welcomeNext');
      const welcomeBack = document.getElementById('welcomeBack');
      const formBack = document.getElementById('formBack');
      const consentForBack = document.getElementById('consentForBack');
      const consentForNext = document.getElementById('consentForNext');
      const identityCard = document.getElementById('identityCard');
      const identityBack = document.getElementById('identityBack');
      const identityNext = document.getElementById('identityNext');
      const consentForInput = document.getElementById('consentForInput');
      const consentOptionButtons = document.querySelectorAll('[data-consent-option]');
      const relativeDetails = document.getElementById('relativeDetails');
      const relativeFields = document.querySelectorAll('[data-relative-field]');
      const clientIpInput = document.getElementById('clientIpInput');
      const clientLocationInput = document.getElementById('clientLocationInput');
      const clientLatitudeInput = document.getElementById('clientLatitudeInput');
      const clientLongitudeInput = document.getElementById('clientLongitudeInput');
      const consentForm = document.getElementById('consentForm');
      const phoneInput = consentForm?.querySelector('input[name="phone"]');
      const otpInput = document.getElementById('otp_code');
      const sendOtpButton = document.getElementById('sendOtpButton');
      const verifyOtpButton = document.getElementById('verifyOtpButton');
      const otpStatus = document.getElementById('otpStatus');
      const originalSendOtpLabel = sendOtpButton?.textContent.trim() || 'Send OTP';
      const originalVerifyOtpLabel = verifyOtpButton?.textContent.trim() || 'Verify OTP';
      const signatureMethodInputs = document.querySelectorAll('input[name="signature_method"]');
      const signatureUploadGroup = document.getElementById('signatureUploadGroup');
      const signatureDrawGroup = document.getElementById('signatureDrawGroup');
      const signatureCanvas = document.getElementById('signatureCanvas');
      const signatureCanvasPlaceholder = document.getElementById('signatureCanvasPlaceholder');
      const signatureCanvasDataInput = document.getElementById('signatureCanvasData');
      const signatureClearButton = document.getElementById('signatureClearButton');
      const signatureCanvasError = document.getElementById('signatureCanvasError');
      const signatureUploadInput = document.getElementById('signatureUploadInput');
      const signatureCroppedDataInput = document.getElementById('signatureCroppedData');
      const signatureUploadPreview = document.getElementById('signatureUploadPreview');
      const signatureUploadPreviewImage = document.getElementById('signatureUploadPreviewImage');
      const signatureUploadError = document.getElementById('signatureUploadError');
      const signatureReCropButton = document.getElementById('signatureReCropButton');
      const signatureCropModal = document.getElementById('signatureCropModal');
      const signatureCropImage = document.getElementById('signatureCropImage');
      const signatureCropClose = document.getElementById('signatureCropClose');
      const signatureCropCancel = document.getElementById('signatureCropCancel');
      const signatureCropConfirm = document.getElementById('signatureCropConfirm');
      const thumbUploadInput = document.getElementById('thumbUploadInput');
      const thumbCroppedDataInput = document.getElementById('thumbCroppedData');
      const thumbUploadPreview = document.getElementById('thumbUploadPreview');
      const thumbUploadPreviewImage = document.getElementById('thumbUploadPreviewImage');
      const thumbUploadError = document.getElementById('thumbUploadError');
      const idProofUploadInput = document.getElementById('idProofUploadInput');
      const idProofCroppedDataInput = document.getElementById('idProofCroppedData');
      const idProofUploadPreview = document.getElementById('idProofUploadPreview');
      const idProofUploadPreviewImage = document.getElementById('idProofUploadPreviewImage');
      const idProofUploadError = document.getElementById('idProofUploadError');
      const idProofReCropButton = document.getElementById('idProofReCropButton');
      const thumbReCropButton = document.getElementById('thumbReCropButton');
      const formStepInput = document.getElementById('formStepInput');
      const primaryDetailsSection = document.getElementById('primaryDetailsSection');
      const witnessSection = document.getElementById('witnessSection');
      const riskPage = document.getElementById('riskPage');
      const legalPage = document.getElementById('legalPage');
      const legalContent = document.getElementById('legalContent');
      const witnessPage = document.getElementById('witnessPage');
      const kycVerifyButton = document.getElementById('kycVerifyButton');
      const kycVerifyStatus = document.getElementById('kycVerifyStatus');
      const kycStep = document.getElementById('kycStep');
      const riskConsentStep = document.getElementById('riskConsentStep');
      const formNextButton = document.getElementById('formNext');
      const witnessBackButton = document.getElementById('witnessBackButton');
      const riskNextButton = document.getElementById('riskNextButton');
      const riskBackButton = document.getElementById('riskBackButton');
      const kycNextButton = document.getElementById('kycNextButton');
      const kycBackButton = document.getElementById('kycBackButton');
      const legalNextButton = document.getElementById('legalNextButton');
      const legalBackButton = document.getElementById('legalBackButton');
      const legalAcknowledgedInput = document.querySelector('input[name="legal_acknowledged"]');
      const witnessUploadInput = document.getElementById('witnessUploadInput');
      const witnessCroppedDataInput = document.getElementById('witnessCroppedData');
      const witnessUploadPreview = document.getElementById('witnessUploadPreview');
      const witnessUploadPreviewImage = document.getElementById('witnessUploadPreviewImage');
      const witnessUploadError = document.getElementById('witnessUploadError');
      const witnessReCropButton = document.getElementById('witnessReCropButton');
      const witnessNameInput = document.getElementById('witness_name');
      const primaryStepFields = primaryDetailsSection?.querySelectorAll('input, select, textarea');
      const riskConsentFilterInputs = document.querySelectorAll('[data-risk-consent-filter]');
      const riskConsentCards = document.querySelectorAll('[data-risk-consent-card]');
      const riskConsentTextInput = document.getElementById('riskConsentTextInput');
      const riskConsentTextInputsByLang = {
        en: document.getElementById('riskConsentTextInputEn'),
        hi: document.getElementById('riskConsentTextInputHi'),
        mr: document.getElementById('riskConsentTextInputMr'),
      };
      const riskConsentDatasetElement = document.getElementById('riskConsentDataset');
      const riskConsentConfigElement = document.getElementById('riskConsentConfig');
      const riskConsentSelectionMessage = document.getElementById('riskConsentSelectionMessage');
      const riskConsentNoMatchMessage = document.getElementById('riskConsentNoMatchMessage');
      let selectedConsentFor = consentForInput?.value || 'self';
      let setSignatureCanvasSize;
      let isDrawing = false;
      let canvasDrawn = false;
      let currentCropFile = null;
      let currentCropType = null;
      let cropperInstance = null;
      let cropObjectUrl = null;
      let cropperReady = false;

      const setClientInfo = ({ ip, city, region, country }) => {
        if (clientIpInput && ip) {
          clientIpInput.value = ip;
        }
        const parts = [city, region, country].filter(Boolean);
        if (clientLocationInput && parts.length) {
          clientLocationInput.value = parts.join(', ');
        }
      };

      let geolocationRequested = false;

      const setClientCoordinates = ({ latitude, longitude }) => {
        if (clientLatitudeInput && typeof latitude === 'number') {
          clientLatitudeInput.value = latitude.toFixed(6);
        }
        if (clientLongitudeInput && typeof longitude === 'number') {
          clientLongitudeInput.value = longitude.toFixed(6);
        }
      };

      const requestGeolocation = () => {
        if (geolocationRequested || !navigator.geolocation || !clientLatitudeInput || !clientLongitudeInput) {
          return;
        }
        geolocationRequested = true;
        navigator.geolocation.getCurrentPosition(
          (position) => {
            if (position?.coords) {
              setClientCoordinates(position.coords);
            }
          },
          (error) => {
            console.warn('Geolocation lookup failed', error);
          },
          {
            enableHighAccuracy: false,
            timeout: 7000,
            maximumAge: 300000,
          }
        );
      };

      const fetchClientInfo = async () => {
        if (!clientIpInput || !clientLocationInput) {
          return;
        }
        const token = '{{ config('services.ipgeolocation.token') }}';
        const endpointOverride = '{{ config('services.ipgeolocation.endpoint') }}';
        try {
          let response;
          if (endpointOverride) {
            response = await fetch(endpointOverride, { headers: { 'Accept': 'application/json' } });
          } else {
            response = await fetch('https://ipinfo.io/json?token=' + encodeURIComponent(token || ''));
          }

          if (!response || !response.ok) {
            return;
          }

          const payload = await response.json();
          if (!payload) {
            return;
          }

          const { ip, city, region, country } = payload;
          setClientInfo({ ip, city, region, country });
        } catch (error) {
          console.error('Unable to fetch client info', error);
        }
      };

      fetchClientInfo();
      requestGeolocation();

      const setKycStatus = (message, type = 'info') => {
        if (!kycVerifyStatus) return;
        kycVerifyStatus.textContent = message || '';
        kycVerifyStatus.classList.remove('hidden', 'text-gray-500', 'text-emerald-600', 'text-rose-600');
        if (!message) {
          kycVerifyStatus.classList.add('hidden');
          return;
        }
        if (type === 'success') {
          kycVerifyStatus.classList.add('text-emerald-600');
        } else if (type === 'error') {
          kycVerifyStatus.classList.add('text-rose-600');
        } else {
          kycVerifyStatus.classList.add('text-gray-500');
        }
      };

      // Add event listener for KYC document type selection
      const kycDocumentType = document.getElementById('kyc_document_type');
      const kycInputOptions = document.getElementById('kycInputOptions');
      
      if (kycDocumentType && kycInputOptions) {
        kycDocumentType.addEventListener('change', () => {
          if (kycDocumentType.value) {
            kycInputOptions.classList.remove('hidden');
            kycInputOptions.classList.add('space-y-3');
          } else {
            kycInputOptions.classList.add('hidden');
          }
        });
        
        // Show options if document type is already selected (for form reloads)
        if (kycDocumentType.value) {
          kycInputOptions.classList.remove('hidden');
          kycInputOptions.classList.add('space-y-3');
        }
      }

      kycVerifyButton?.addEventListener('click', async () => {
        if (!consentForm) {
          return;
        }

        const url = kycVerifyButton.getAttribute('data-kyc-verify-url');
        if (!url) {
          setKycStatus('KYC verification is not available right now.', 'error');
          return;
        }

        const orgInput = consentForm.querySelector('input[name="organization_id"]');
        const docTypeSelect = document.getElementById('kyc_document_type');
        const inputModeRadio = consentForm.querySelector('input[name="kyc_input_mode"]:checked');
        const docNumberInput = document.getElementById('kyc_document_number');
        const docImageInput = document.getElementById('kyc_document_image');
        const tokenInput = consentForm.querySelector('input[name="_token"]');

        const organizationId = orgInput?.value || '';
        const documentType = docTypeSelect?.value || '';
        const inputMode = inputModeRadio?.value || '';
        const documentNumber = docNumberInput?.value || '';

        if (!organizationId) {
          setKycStatus('Organization ID is missing for KYC.', 'error');
          return;
        }

        if (!documentType) {
          setKycStatus('Please select a document type.', 'error');
          docTypeSelect?.focus();
          return;
        }

        if (!inputMode) {
          setKycStatus('Please choose whether to enter a document number or upload an image.', 'error');
          return;
        }

        if (inputMode === 'number' && !documentNumber) {
          setKycStatus('Please enter the document number.', 'error');
          docNumberInput?.focus();
          return;
        }

        if (inputMode === 'image' && !(docImageInput?.files?.length > 0)) {
          setKycStatus('Please upload the document image.', 'error');
          docImageInput?.focus();
          return;
        }

        const formData = new FormData();
        if (tokenInput?.value) {
          formData.append('_token', tokenInput.value);
        }
        formData.append('organization_id', organizationId);
        formData.append('kyc_document_type', documentType);
        formData.append('kyc_input_mode', inputMode);
        if (inputMode === 'number') {
          formData.append('kyc_document_number', documentNumber);
        } else if (inputMode === 'image' && docImageInput?.files?.length) {
          formData.append('kyc_document_image', docImageInput.files[0]);
        }

        setKycStatus('Verifying KYC...', 'info');
        kycVerifyButton.disabled = true;

        try {
          const response = await fetch(url, {
            method: 'POST',
            body: formData,
          });

          const payload = await response.json().catch(() => null);

          if (!response.ok || !payload || payload.status !== 'success') {
            const message = payload?.message || 'Unable to verify KYC. Please check details and try again.';
            setKycStatus(message, 'error');
            return;
          }

          setKycStatus(payload.message || 'KYC verified successfully.', 'success');
        } catch (error) {
          console.error('KYC verification failed', error);
          setKycStatus('KYC verification failed due to a network error. Please try again.', 'error');
        } finally {
          kycVerifyButton.disabled = false;
        }
      });

      const showOtpStatus = (message, type = 'info') => {
        if (!otpStatus) return;
        otpStatus.textContent = message || '';
        otpStatus.classList.remove('text-emerald-600', 'text-rose-600', 'text-gray-500');
        if (type === 'success') {
          otpStatus.classList.add('text-emerald-600');
        } else if (type === 'error') {
          otpStatus.classList.add('text-rose-600');
        } else if (message) {
          otpStatus.classList.add('text-gray-500');
        }
      };

      if (sendOtpButton && consentForm) {
        const sendOtpUrl = sendOtpButton.dataset.sendOtpUrl;
        const orgInput = consentForm.querySelector('input[name="organization_id"]');
        const csrfInput = consentForm.querySelector('input[name="_token"]');

        sendOtpButton.addEventListener('click', async () => {
          if (!sendOtpUrl || !phoneInput || !orgInput || !csrfInput) {
            return;
          }

          const phone = (phoneInput.value || '').trim();
          if (!phone) {
            showOtpStatus('Please enter mobile number first.', 'error');
            phoneInput.focus();
            return;
          }

          showOtpStatus('Sending OTP...', 'info');
          sendOtpButton.disabled = true;
          sendOtpButton.textContent = 'Sending...';

          try {
            const formData = new FormData();
            formData.append('_token', csrfInput.value);
            formData.append('organization_id', orgInput.value || '');
            formData.append('phone', phone);

            const response = await fetch(sendOtpUrl, {
              method: 'POST',
              body: formData,
              headers: {
                'Accept': 'application/json',
              },
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok || (payload && payload.status === 'error')) {
              const message = (payload && payload.message) || 'Unable to send OTP. Please try again.';
              showOtpStatus(message, 'error');
              // In case of error, allow immediate retry
              if (!sendOtpButton.dataset.otpLocked) {
                sendOtpButton.disabled = false;
                sendOtpButton.textContent = originalSendOtpLabel;
              }
            } else {
              const message = (payload && payload.message) || 'OTP sent to your mobile number.';
              showOtpStatus(message, 'success');
              if (otpInput) {
                otpInput.focus();
              }

              sendOtpButton.textContent = 'OTP Sent';

              // Cooldown: keep Send OTP disabled for 20 seconds unless permanently locked after verification
              const cooldownMs = 20000;
              setTimeout(() => {
                if (sendOtpButton && !sendOtpButton.dataset.otpLocked) {
                  sendOtpButton.disabled = false;
                  sendOtpButton.textContent = originalSendOtpLabel;
                }
              }, cooldownMs);
            }
          } catch (error) {
            showOtpStatus('Network error while sending OTP. Please try again.', 'error');
            if (!sendOtpButton.dataset.otpLocked) {
              sendOtpButton.disabled = false;
              sendOtpButton.textContent = originalSendOtpLabel;
            }
          }
        });

        if (phoneInput) {
          phoneInput.addEventListener('input', () => {
            showOtpStatus('', 'info');
          });
        }
      }

      if (verifyOtpButton && consentForm) {
        const verifyOtpUrl = verifyOtpButton.dataset.verifyOtpUrl;
        const orgInput = consentForm.querySelector('input[name="organization_id"]');
        const csrfInput = consentForm.querySelector('input[name="_token"]');

        verifyOtpButton.addEventListener('click', async () => {
          if (!verifyOtpUrl || !phoneInput || !otpInput || !orgInput || !csrfInput) {
            return;
          }

          const phone = (phoneInput.value || '').trim();
          const otp = (otpInput.value || '').trim();

          if (!phone) {
            showOtpStatus('Please enter mobile number first.', 'error');
            phoneInput.focus();
            return;
          }

          if (!otp) {
            showOtpStatus('Please enter OTP first.', 'error');
            otpInput.focus();
            return;
          }

          showOtpStatus('Verifying OTP...', 'info');
          verifyOtpButton.disabled = true;
          verifyOtpButton.textContent = 'Verifying...';

          try {
            const formData = new FormData();
            formData.append('_token', csrfInput.value);
            formData.append('organization_id', orgInput.value || '');
            formData.append('phone', phone);
            formData.append('otp_code', otp);

            const response = await fetch(verifyOtpUrl, {
              method: 'POST',
              body: formData,
              headers: {
                'Accept': 'application/json',
              },
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok || (payload && payload.status === 'error')) {
              const message = (payload && payload.message) || 'Unable to verify OTP. Please try again.';
              showOtpStatus(message, 'error');
              // Allow user to try again with a new/correct OTP
              verifyOtpButton.disabled = false;
              verifyOtpButton.textContent = originalVerifyOtpLabel;
            } else {
              const message = (payload && payload.message) || 'OTP verified successfully.';
              showOtpStatus(message, 'success');
              if (phoneInput) {
                phoneInput.readOnly = true;
                phoneInput.classList.add('bg-gray-100', 'cursor-not-allowed');
              }
              if (otpInput) {
                otpInput.readOnly = true;
                otpInput.classList.add('bg-gray-100', 'cursor-not-allowed');
              }
              if (sendOtpButton) {
                sendOtpButton.disabled = true;
                sendOtpButton.dataset.otpLocked = '1';
                sendOtpButton.textContent = 'Verified';
              }
              verifyOtpButton.disabled = true;
              verifyOtpButton.textContent = 'Verified';
            }
          } catch (error) {
            showOtpStatus('Network error while verifying OTP. Please try again.', 'error');
            // On network failure also allow retry
            verifyOtpButton.disabled = false;
            verifyOtpButton.textContent = originalVerifyOtpLabel;
          }
        });
      }

      // Add automatic OTP verification when 6 digits are entered
      if (otpInput) {
        otpInput.addEventListener('input', async () => {
          const otp = (otpInput.value || '').trim();
          
          // Auto-verify when exactly 6 digits are entered
          if (otp.length === 6 && /^\d{6}$/.test(otp)) {
            // Check if OTP has been sent and verification is not already done
            if (sendOtpButton && !sendOtpButton.dataset.otpLocked && phoneInput) {
              const phone = (phoneInput.value || '').trim();
              
              if (phone) {
                const verifyOtpUrl = verifyOtpButton?.dataset.verifyOtpUrl;
                const orgInput = consentForm?.querySelector('input[name="organization_id"]');
                const csrfInput = consentForm?.querySelector('input[name="_token"]');

                if (verifyOtpUrl && orgInput && csrfInput) {
                  showOtpStatus('Verifying OTP...', 'info');
                  verifyOtpButton.disabled = true;
                  verifyOtpButton.textContent = 'Verifying...';

                  try {
                    const formData = new FormData();
                    formData.append('_token', csrfInput.value);
                    formData.append('organization_id', orgInput.value || '');
                    formData.append('phone', phone);
                    formData.append('otp_code', otp);

                    const response = await fetch(verifyOtpUrl, {
                      method: 'POST',
                      body: formData,
                      headers: {
                        'Accept': 'application/json',
                      },
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok || (payload && payload.status === 'error')) {
                      const message = (payload && payload.message) || 'Invalid OTP. Please try again.';
                      showOtpStatus(message, 'error');
                      verifyOtpButton.disabled = false;
                      verifyOtpButton.textContent = 'Verify OTP';
                    } else {
                      const message = (payload && payload.message) || 'OTP verified successfully.';
                      showOtpStatus(message, 'success');
                      
                      // Auto-update verification state
                      if (phoneInput) {
                        phoneInput.readOnly = true;
                        phoneInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                      }
                      if (otpInput) {
                        otpInput.readOnly = true;
                        otpInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                      }
                      if (sendOtpButton) {
                        sendOtpButton.disabled = true;
                        sendOtpButton.dataset.otpLocked = '1';
                        sendOtpButton.textContent = 'Verified';
                      }
                      verifyOtpButton.disabled = true;
                      verifyOtpButton.textContent = 'Verified';
                    }
                  } catch (error) {
                    showOtpStatus('Network error while verifying OTP. Please try again.', 'error');
                    verifyOtpButton.disabled = false;
                    verifyOtpButton.textContent = 'Verify OTP';
                  }
                }
              }
            }
          } else if (otp.length > 0) {
            // Clear status when user is typing (but not yet 6 digits)
            showOtpStatus('', 'info');
          }
        });
      }

      const showFormStep = (step, options = {}) => {
        const normalized = step === 'witness' ? 'witness' : 'details';
        const { scroll = true } = options;

        if (formStepInput) {
          formStepInput.value = normalized;
        }

        if (primaryDetailsSection) {
          primaryDetailsSection.classList.toggle('hidden', normalized !== 'details');
        }

        if (witnessSection) {
          witnessSection.classList.toggle('hidden', normalized !== 'witness');
        }

        if (normalized === 'witness') {
          // Default to showing KYC first when entering the witness step
          if (riskPage) {
            riskPage.classList.remove('hidden');
          }
          if (kycStep) {
            kycStep.classList.remove('hidden');
          }
          if (riskConsentStep) {
            riskConsentStep.classList.add('hidden');
          }
          if (legalPage) {
            legalPage.classList.add('hidden');
          }
          if (witnessPage) {
            witnessPage.classList.add('hidden');
          }
        }

        if (normalized === 'witness') {
          if (scroll) {
            witnessSection?.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
          // Focus will be moved to witness fields when witness page becomes active
        } else if (scroll) {
          primaryDetailsSection?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      };

      showFormStep(formStepInput?.value || 'details', { scroll: false });

      const resetUploadSelection = (type = 'signature') => {
        if (type === 'thumb') {
          if (thumbUploadInput) {
            thumbUploadInput.value = '';
          }
          thumbCroppedDataInput && (thumbCroppedDataInput.value = '');
          if (thumbUploadPreviewImage) {
            thumbUploadPreviewImage.src = '';
          }
          thumbUploadPreview?.classList.add('hidden');
          thumbUploadError?.classList.add('hidden');
        } else if (type === 'id_proof') {
          if (idProofUploadInput) {
            idProofUploadInput.value = '';
          }
          idProofCroppedDataInput && (idProofCroppedDataInput.value = '');
          if (idProofUploadPreviewImage) {
            idProofUploadPreviewImage.src = '';
          }
          idProofUploadPreview?.classList.add('hidden');
          idProofUploadError?.classList.add('hidden');
        } else if (type === 'witness') {
          if (witnessUploadInput) {
            witnessUploadInput.value = '';
          }
          witnessCroppedDataInput && (witnessCroppedDataInput.value = '');
          if (witnessUploadPreviewImage) {
            witnessUploadPreviewImage.src = '';
          }
          witnessUploadPreview?.classList.add('hidden');
          witnessUploadError?.classList.add('hidden');
        } else {
          if (signatureUploadInput) {
            signatureUploadInput.value = '';
          }
          signatureCroppedDataInput && (signatureCroppedDataInput.value = '');
          if (signatureUploadPreviewImage) {
            signatureUploadPreviewImage.src = '';
          }
          signatureUploadPreview?.classList.add('hidden');
          signatureUploadError?.classList.add('hidden');
        }
        currentCropFile = null;
      };

      const destroyCropper = () => {
        if (cropperInstance) {
          cropperInstance.destroy();
          cropperInstance = null;
        }
        cropperReady = false;
      };

      const closeCropModal = (resetSelection = false) => {
        destroyCropper();
        if (signatureCropModal) {
          signatureCropModal.classList.add('hidden');
          signatureCropModal.classList.remove('flex');
        }
        if (signatureCropImage) {
          signatureCropImage.src = '';
        }
        if (cropObjectUrl) {
          URL.revokeObjectURL(cropObjectUrl);
          cropObjectUrl = null;
        }
        if (resetSelection) {
          resetUploadSelection(currentCropType || 'signature');
        }
      };

      const initCropper = () => {
        if (!signatureCropImage) {
          return;
        }
        destroyCropper();
        cropperInstance = new Cropper(signatureCropImage, {
          viewMode: 1,
          responsive: true,
          autoCropArea: 1,
          background: false,
          movable: true,
          zoomable: true,
          ready() {
            cropperReady = true;
          },
        });
      };

      const openCropModal = (file, type = 'signature') => {
        if (!signatureCropModal || !signatureCropImage || !file) {
          return;
        }
        destroyCropper();
        cropObjectUrl = URL.createObjectURL(file);
        signatureCropImage.src = cropObjectUrl;
        signatureCropModal.classList.remove('hidden');
        signatureCropModal.classList.add('flex');
        if (signatureCropImage.complete && signatureCropImage.naturalWidth) {
          initCropper();
        } else {
          signatureCropImage.addEventListener('load', initCropper, { once: true });
        }
      };

      const handleUploadFile = (file, type = 'signature') => {
        if (!file) {
          resetUploadSelection(type);
          return;
        }

        let errorElement;
        if (type === 'signature') {
          errorElement = signatureUploadError;
        } else if (type === 'thumb') {
          errorElement = thumbUploadError;
        } else if (type === 'witness') {
          errorElement = witnessUploadError;
        } else if (type === 'id_proof') {
          errorElement = idProofUploadError;
        }
        errorElement?.classList.add('hidden');

        if (!file.type.startsWith('image/')) {
          resetUploadSelection(type);
          if (errorElement) {
            errorElement.textContent = 'Unsupported file type. Please upload an image file.';
            errorElement.classList.remove('hidden');
          }
          return;
        }

        currentCropFile = file;
        currentCropType = type;
        openCropModal(file, type);
      };

      const applyCrop = () => {
        if (!cropperInstance) {
          if (currentCropFile && currentCropFile.type && currentCropFile.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = () => {
              const dataUrl = reader.result;
              if (!dataUrl) {
                return;
              }
              if (currentCropType === 'signature') {
                signatureUploadPreviewImage && (signatureUploadPreviewImage.src = dataUrl);
                signatureUploadPreview?.classList.remove('hidden');
                signatureCroppedDataInput && (signatureCroppedDataInput.value = dataUrl);
              } else if (currentCropType === 'thumb') {
                thumbUploadPreviewImage && (thumbUploadPreviewImage.src = dataUrl);
                thumbUploadPreview?.classList.remove('hidden');
                thumbCroppedDataInput && (thumbCroppedDataInput.value = dataUrl);
              } else if (currentCropType === 'id_proof') {
                idProofUploadPreviewImage && (idProofUploadPreviewImage.src = dataUrl);
                idProofUploadPreview?.classList.remove('hidden');
                idProofCroppedDataInput && (idProofCroppedDataInput.value = dataUrl);
              } else if (currentCropType === 'witness') {
                witnessUploadPreviewImage && (witnessUploadPreviewImage.src = dataUrl);
                witnessUploadPreview?.classList.remove('hidden');
                witnessCroppedDataInput && (witnessCroppedDataInput.value = dataUrl);
              }
              signatureUploadError?.classList.add('hidden');
              thumbUploadError?.classList.add('hidden');
              witnessUploadError?.classList.add('hidden');
              closeCropModal();
            };
            reader.readAsDataURL(currentCropFile);
          }
          return;
        }

        let canvas = null;
        try {
          canvas = cropperInstance.getCroppedCanvas({ width: 1024, imageSmoothingQuality: 'high' });
        } catch (error) {
          canvas = null;
        }

        if (!canvas || !canvas.width || !canvas.height) {
          try {
            canvas = cropperInstance.getCroppedCanvas();
          } catch (error) {
            canvas = null;
          }
        }

        if (!canvas || !canvas.width || !canvas.height) {
          if (currentCropFile && currentCropFile.type && currentCropFile.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = () => {
              const dataUrl = reader.result;
              if (!dataUrl) {
                return;
              }
              if (currentCropType === 'signature') {
                signatureUploadPreviewImage && (signatureUploadPreviewImage.src = dataUrl);
                signatureUploadPreview?.classList.remove('hidden');
                signatureCroppedDataInput && (signatureCroppedDataInput.value = dataUrl);
              } else if (currentCropType === 'thumb') {
                thumbUploadPreviewImage && (thumbUploadPreviewImage.src = dataUrl);
                thumbUploadPreview?.classList.remove('hidden');
                thumbCroppedDataInput && (thumbCroppedDataInput.value = dataUrl);
              } else if (currentCropType === 'id_proof') {
                idProofUploadPreviewImage && (idProofUploadPreviewImage.src = dataUrl);
                idProofUploadPreview?.classList.remove('hidden');
                idProofCroppedDataInput && (idProofCroppedDataInput.value = dataUrl);
              }
              signatureUploadError?.classList.add('hidden');
              thumbUploadError?.classList.add('hidden');
              closeCropModal();
            };
            reader.readAsDataURL(currentCropFile);
          }
          return;
        }

        const dataUrl = canvas.toDataURL('image/png');
        if (currentCropType === 'signature') {
          signatureUploadPreviewImage && (signatureUploadPreviewImage.src = dataUrl);
          signatureUploadPreview?.classList.remove('hidden');
          signatureCroppedDataInput && (signatureCroppedDataInput.value = dataUrl);
        } else if (currentCropType === 'thumb') {
          thumbUploadPreviewImage && (thumbUploadPreviewImage.src = dataUrl);
          thumbUploadPreview?.classList.remove('hidden');
          thumbCroppedDataInput && (thumbCroppedDataInput.value = dataUrl);
        } else if (currentCropType === 'id_proof') {
          idProofUploadPreviewImage && (idProofUploadPreviewImage.src = dataUrl);
          idProofUploadPreview?.classList.remove('hidden');
          idProofCroppedDataInput && (idProofCroppedDataInput.value = dataUrl);
        } else if (currentCropType === 'witness') {
          witnessUploadPreviewImage && (witnessUploadPreviewImage.src = dataUrl);
          witnessUploadPreview?.classList.remove('hidden');
          witnessCroppedDataInput && (witnessCroppedDataInput.value = dataUrl);
        }
        signatureUploadError?.classList.add('hidden');
        thumbUploadError?.classList.add('hidden');
        idProofUploadError?.classList.add('hidden');
        witnessUploadError?.classList.add('hidden');

        canvas.toBlob((blob) => {
          if (!blob) {
            closeCropModal();
            return;
          }
          const cleanName = currentCropFile?.name ? currentCropFile.name.replace(/\.[^/.]+$/, '') : 'signature';
          const croppedFile = new File([blob], `${cleanName || 'signature'}-cropped.png`, { type: 'image/png' });
          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(croppedFile);
          if (currentCropType === 'signature' && signatureUploadInput) {
            signatureUploadInput.files = dataTransfer.files;
          } else if (currentCropType === 'thumb' && thumbUploadInput) {
            thumbUploadInput.files = dataTransfer.files;
          } else if (currentCropType === 'id_proof' && idProofUploadInput) {
            idProofUploadInput.files = dataTransfer.files;
          } else if (currentCropType === 'witness' && witnessUploadInput) {
            witnessUploadInput.files = dataTransfer.files;
          }
          currentCropFile = croppedFile;
          closeCropModal();
        }, 'image/png');
      };

      const updateSignatureVisibility = () => {
        const selected = document.querySelector('input[name="signature_method"]:checked')?.value || 'draw';
        if (selected === 'upload') {
          signatureDrawGroup?.classList.add('hidden');
          signatureUploadGroup?.classList.remove('hidden');
          signatureCanvasError?.classList.add('hidden');
          if (signatureCanvasDataInput) {
            signatureCanvasDataInput.value = '';
          }
          if (signatureCroppedDataInput?.value && signatureUploadPreview && signatureUploadPreviewImage) {
            signatureUploadPreviewImage.src = signatureCroppedDataInput.value;
            signatureUploadPreview.classList.remove('hidden');
          }
        } else {
          signatureDrawGroup?.classList.remove('hidden');
          signatureUploadGroup?.classList.add('hidden');
          resetUploadSelection();
          closeCropModal();
          setTimeout(() => {
            setSignatureCanvasSize?.(canvasDrawn);
          }, 50);
        }
      };

      if (signatureCanvas && signatureCanvasDataInput) {
        const ctx = signatureCanvas.getContext('2d');
        signatureCanvas.style.touchAction = 'none';

        setSignatureCanvasSize = (preserve = true) => {
          const ratio = window.devicePixelRatio || 1;
          const width = signatureCanvas.clientWidth;
          const height = signatureCanvas.clientHeight;

          if (!width || !height) {
            return;
          }

          let existingDataUrl = null;
          if (preserve && signatureCanvas.width && signatureCanvas.height) {
            existingDataUrl = signatureCanvas.toDataURL('image/png');
          }

          const targetWidth = width * ratio;
          const targetHeight = height * ratio;

          if (signatureCanvas.width !== targetWidth || signatureCanvas.height !== targetHeight) {
            signatureCanvas.width = targetWidth;
            signatureCanvas.height = targetHeight;
          }

          ctx.setTransform(1, 0, 0, 1, 0, 0);
          ctx.scale(ratio, ratio);

          ctx.lineWidth = 2;
          ctx.lineJoin = 'round';
          ctx.lineCap = 'round';
          ctx.strokeStyle = '#111827';

          if (existingDataUrl) {
            const image = new Image();
            image.onload = () => {
              ctx.drawImage(image, 0, 0, width, height);
              signatureCanvasPlaceholder?.classList.add('hidden');
            };
            image.src = existingDataUrl;
          }
        };

        setSignatureCanvasSize(false);

        const restoreExistingSignature = () => {
          const dataUrl = signatureCanvasDataInput.value;
          if (dataUrl) {
            const image = new Image();
            image.onload = () => {
              setSignatureCanvasSize(false);
              ctx.drawImage(image, 0, 0, signatureCanvas.clientWidth, signatureCanvas.clientHeight);
              canvasDrawn = true;
              signatureCanvasPlaceholder?.classList.add('hidden');
            };
            image.src = dataUrl;
          } else {
            signatureCanvasPlaceholder?.classList.remove('hidden');
          }
        };

        const getCanvasCoordinates = (event) => {
          const rect = signatureCanvas.getBoundingClientRect();
          const scaleX = signatureCanvas.width / rect.width;
          const scaleY = signatureCanvas.height / rect.height;
          if (event.touches && event.touches.length > 0) {
            return {
              x: (event.touches[0].clientX - rect.left) * scaleX,
              y: (event.touches[0].clientY - rect.top) * scaleY,
            };
          }
          return {
            x: (event.clientX - rect.left) * scaleX,
            y: (event.clientY - rect.top) * scaleY,
          };
        };

        const startDrawing = (event) => {
          if (document.querySelector('input[name="signature_method"]:checked')?.value !== 'draw') {
            return;
          }
          event.preventDefault();
          const { x, y } = getCanvasCoordinates(event);
          ctx.beginPath();
          ctx.moveTo(x, y);
          isDrawing = true;
          canvasDrawn = true;
          signatureCanvasPlaceholder?.classList.add('hidden');
        };

        const drawLine = (event) => {
          if (!isDrawing) {
            return;
          }
          event.preventDefault();
          const { x, y } = getCanvasCoordinates(event);
          ctx.lineTo(x, y);
          ctx.stroke();
        };

        const stopDrawing = () => {
          if (!isDrawing) {
            return;
          }
          isDrawing = false;
          ctx.closePath();
          signatureCanvasDataInput.value = signatureCanvas.toDataURL('image/png');
        };

        const clearCanvas = () => {
          ctx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
          signatureCanvasDataInput.value = '';
          signatureCanvasPlaceholder?.classList.remove('hidden');
          canvasDrawn = false;
          signatureCanvasError?.classList.add('hidden');
        };

        signatureCanvas.addEventListener('pointerdown', startDrawing);
        signatureCanvas.addEventListener('pointermove', drawLine);
        document.addEventListener('pointerup', stopDrawing);
        signatureCanvas.addEventListener('pointerleave', stopDrawing);

        signatureCanvas.addEventListener('touchstart', startDrawing, { passive: false });
        signatureCanvas.addEventListener('touchmove', drawLine, { passive: false });
        signatureCanvas.addEventListener('touchend', stopDrawing, { passive: false });

        signatureClearButton?.addEventListener('click', clearCanvas);
        window.addEventListener('resize', () => {
          setSignatureCanvasSize?.(canvasDrawn);
        });

        restoreExistingSignature();

        consentForm?.addEventListener('submit', (event) => {
          // Ensure latest selected risk consent text is captured before submit
          try {
            updateRiskConsentText?.();
          } catch (e) {
            console.warn('Unable to update risk consent text before submit', e);
          }

          const method = document.querySelector('input[name="signature_method"]:checked')?.value || 'draw';
          if (method === 'draw') {
            if (canvasDrawn && signatureCanvas) {
              try {
                signatureCanvasDataInput.value = signatureCanvas.toDataURL('image/png');
              } catch (error) {
                console.error('Unable to serialize signature canvas', error);
              }
            }
          }
        });
      }

      signatureMethodInputs.forEach((input) => {
        input.addEventListener('change', () => {
          updateSignatureVisibility();
          if (input.value === 'draw' && input.checked) {
            setTimeout(() => setSignatureCanvasSize?.(canvasDrawn), 50);
          }
          if (input.value === 'upload' && input.checked && signatureUploadInput?.files?.length) {
            const file = signatureUploadInput.files[0];
            if (file.type.startsWith('image/') && !signatureCroppedDataInput?.value) {
              handleUploadFile(file);
            }
          }
        });
      });

      updateSignatureVisibility();

      const updateConsentOptions = function () {
        consentOptionButtons.forEach(function (button) {
          const value = button.getAttribute('data-consent-option');
          const isActive = value === selectedConsentFor;
          button.classList.toggle('border-gray-900', isActive);
          button.classList.toggle('bg-gray-900/5', isActive);
          button.classList.toggle('border-gray-200', !isActive);
          button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });

        if (consentForInput) {
          consentForInput.value = selectedConsentFor;
        }

        const isRelative = selectedConsentFor === 'relative';
        if (relativeDetails) {
          relativeDetails.classList.toggle('hidden', !isRelative);
        }
        relativeFields.forEach(function (field) {
          if (isRelative) {
            field.removeAttribute('disabled');
            field.setAttribute('required', 'required');
          } else {
            field.setAttribute('disabled', 'disabled');
            field.removeAttribute('required');
          }
        });
      };

      consentOptionButtons.forEach(function (button) {
        const value = button.getAttribute('data-consent-option');
        if (!value) {
          return;
        }
        button.addEventListener('click', function () {
          selectedConsentFor = value;
          updateConsentOptions();
        });
      });

      updateConsentOptions();

      let riskConsentDataset = [];
      let riskConsentDatasetById = {};
      try {
        riskConsentDataset = JSON.parse(riskConsentDatasetElement?.textContent || '[]') || [];
        if (!Array.isArray(riskConsentDataset)) {
          riskConsentDataset = [];
        }
      } catch (error) {
        console.warn('Unable to parse risk consent dataset', error);
        riskConsentDataset = [];
      }

      riskConsentDatasetById = (riskConsentDataset || []).reduce((accumulator, entry) => {
        if (entry && typeof entry.id !== 'undefined') {
          accumulator[String(entry.id)] = entry;
        }
        return accumulator;
      }, {});

      const defaultRiskConsentConfig = {
        activeLang: (document.documentElement.getAttribute('lang') || 'en').toLowerCase(),
        supportedLangs: ['en', 'hi', 'mr'],
      };

      let riskConsentConfig = { ...defaultRiskConsentConfig };
      try {
        const parsedConfig = JSON.parse(riskConsentConfigElement?.textContent || '{}');
        if (parsedConfig && typeof parsedConfig === 'object') {
          riskConsentConfig = { ...riskConsentConfig, ...parsedConfig };
        }
      } catch (error) {
        console.warn('Unable to parse risk consent config', error);
      }

      const supportedRiskConsentLangs = Array.isArray(riskConsentConfig.supportedLangs) && riskConsentConfig.supportedLangs.length
        ? riskConsentConfig.supportedLangs
        : defaultRiskConsentConfig.supportedLangs;

      const activeRiskConsentLang = supportedRiskConsentLangs.includes(riskConsentConfig.activeLang)
        ? riskConsentConfig.activeLang
        : defaultRiskConsentConfig.activeLang;

      const visibleRiskConsentCards = () => Array
        .from(riskConsentCards || [])
        .filter((card) => !card.classList.contains('hidden'));

      const toggleRiskConsentMessages = ({ anySelected, anyVisible }) => {
        if (riskConsentSelectionMessage) {
          riskConsentSelectionMessage.classList.toggle('hidden', anySelected);
        }

        if (riskConsentNoMatchMessage) {
          riskConsentNoMatchMessage.classList.toggle('hidden', !anySelected || anyVisible);
        }
      };

      const applyRiskConsentFilter = () => {
        const selected = new Set(
          Array.from(riskConsentFilterInputs || [])
            .filter((chip) => chip.checked)
            .map((chip) => chip.value)
        );

        const anySelected = selected.size > 0;

        (riskConsentCards || []).forEach((card) => {
          const categoryId = card.getAttribute('data-category-id') || 'uncategorized';
          const shouldShow = anySelected && selected.has(categoryId);
          card.classList.toggle('hidden', !shouldShow);
        });

        const anyVisible = anySelected && visibleRiskConsentCards().length > 0;

        updateRiskConsentText();
        toggleRiskConsentMessages({ anySelected, anyVisible });

        return anyVisible;
      };

      const updateRiskConsentText = () => {
        const cards = visibleRiskConsentCards();
        const visibleIds = cards.map((card) => card.getAttribute('data-consent-id')).filter(Boolean);

        const textByLang = supportedRiskConsentLangs.reduce((accumulator, lang) => {
          accumulator[lang] = [];
          return accumulator;
        }, {});

        const entries = visibleIds
          .map((id) => riskConsentDatasetById[String(id)] || null)
          .filter(Boolean);

        entries.forEach((entry, index) => {
          const heading = entry.title ? `# ${entry.title}` : `# Consent ${index + 1}`;

          supportedRiskConsentLangs.forEach((lang) => {
            const content = entry.contents?.[lang] ? String(entry.contents[lang]).trim() : '';
            textByLang[lang] = textByLang[lang] || [];
            textByLang[lang].push(content ? `${heading}\n${content}` : heading);
          });
        });

        supportedRiskConsentLangs.forEach((lang) => {
          const textarea = riskConsentTextInputsByLang?.[lang];
          if (textarea) {
            textarea.value = (textByLang[lang] || []).join("\n\n---\n\n");
          }
        });

        if (riskConsentTextInput) {
          riskConsentTextInput.value = (textByLang[activeRiskConsentLang] || []).join("\n\n---\n\n");
        }
      };

      const validatePrimaryStep = () => {
        if (!primaryStepFields) {
          return true;
        }

        // Check mobile verification first
        if (phoneInput && sendOtpButton) {
          const phone = (phoneInput.value || '').trim();
          if (phone) {
            // Check if OTP is verified (button shows "Verified" and is disabled)
            if (!sendOtpButton.dataset.otpLocked || sendOtpButton.textContent !== 'Verified') {
              showOtpStatus('Please verify your mobile number before proceeding.', 'error');
              if (otpInput) {
                otpInput.focus();
              } else {
                sendOtpButton.focus();
              }
              return false;
            }
          } else {
            showOtpStatus('Please enter and verify your mobile number before proceeding.', 'error');
            phoneInput.focus();
            return false;
          }
        }

        // Check signature validation
        const signatureMethod = document.querySelector('input[name="signature_method"]:checked')?.value || 'draw';
        let signatureValid = false;
        
        if (signatureMethod === 'draw') {
          // Check if canvas has been drawn on
          if (canvasDrawn && signatureCanvas) {
            signatureValid = true;
          } else {
            // Show error and focus on canvas
            if (signatureCanvasError) {
              signatureCanvasError.classList.remove('hidden');
            }
            signatureCanvas?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            showOtpStatus('Please provide your signature before proceeding.', 'error');
            return false;
          }
        } else if (signatureMethod === 'upload') {
          // Check if signature file has been uploaded
          const signatureUpload = document.querySelector('input[name="signature_upload"]');
          const signatureCroppedData = document.querySelector('input[name="signature_cropped_data"]');
          
          if (signatureUpload?.files?.length > 0 && signatureCroppedData?.value) {
            signatureValid = true;
          } else {
            // Show error and focus on upload input
            if (signatureUploadError) {
              signatureUploadError.classList.remove('hidden');
            }
            signatureUpload?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            showOtpStatus('Please upload your signature image before proceeding.', 'error');
            return false;
          }
        }

        if (!signatureValid) {
          showOtpStatus('Please provide your signature before proceeding.', 'error');
          return false;
        }

        for (const field of primaryStepFields) {
          if (!field || field.disabled) {
            continue;
          }

          if (field.closest('.hidden')) {
            continue;
          }

          if (typeof field.checkValidity === 'function' && !field.checkValidity()) {
            if (typeof field.reportValidity === 'function') {
              field.reportValidity();
            }
            field.focus();
            return false;
          }
        }

        return true;
      };

      formNextButton?.addEventListener('click', () => {
        if (!validatePrimaryStep()) {
          return;
        }

        // After primary details, always move to witness step starting at KYC
        showFormStep('witness');
      });

      kycNextButton?.addEventListener('click', (event) => {
        event.preventDefault();
        // Move from KYC card to risk consent categories/cards within the risk page
        if (kycStep) {
          kycStep.classList.add('hidden');
        }
        if (riskConsentStep) {
          riskConsentStep.classList.remove('hidden');
          riskConsentStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // When entering risk consent step, ensure filters/messages are in a consistent state
        if (riskConsentFilterInputs?.length) {
          applyRiskConsentFilter();
        } else if (riskConsentSelectionMessage) {
          riskConsentSelectionMessage.classList.remove('hidden');
        }
      });

      kycBackButton?.addEventListener('click', (event) => {
        event.preventDefault();
        // From KYC, go back to primary details step
        showFormStep('details');
      });

      riskNextButton?.addEventListener('click', () => {
        // Move from risk consent to legal acknowledgements
        updateRiskConsentText();
        if (riskPage) {
          riskPage.classList.add('hidden');
        }
        if (legalPage) {
          // Terms & Conditions detail should be hidden by default when opening legal page
          if (legalContent && !legalContent.classList.contains('hidden')) {
            legalContent.classList.add('hidden');
          }
          legalPage.classList.remove('hidden');
          legalPage.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });

      riskBackButton?.addEventListener('click', (event) => {
        event.preventDefault();
        // From risk consent, go back to KYC within the risk page if possible, else to primary details
        if (riskConsentStep && kycStep) {
          riskConsentStep.classList.add('hidden');
          kycStep.classList.remove('hidden');
          kycStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
          return;
        }

        showFormStep('details');
      });

      legalNextButton?.addEventListener('click', (event) => {
        event.preventDefault();
        // Require Terms & Conditions checkbox before moving to witness details
        if (legalAcknowledgedInput && !legalAcknowledgedInput.checked) {
          // Trigger browser validation UI if possible
          try {
            if (typeof legalAcknowledgedInput.reportValidity === 'function') {
              legalAcknowledgedInput.reportValidity();
            } else {
              legalAcknowledgedInput.focus();
            }
          } catch (e) {
            legalAcknowledgedInput.focus();
          }
          return;
        }

        // From legal acknowledgements, move to witness details
        if (legalPage) {
          legalPage.classList.add('hidden');
        }
        if (witnessPage) {
          witnessPage.classList.remove('hidden');
          witnessPage.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        witnessNameInput?.focus();
      });

      legalBackButton?.addEventListener('click', (event) => {
        event.preventDefault();
        // From legal acknowledgements, go back to risk consent step (not KYC)
        if (legalPage) {
          legalPage.classList.add('hidden');
        }
        if (riskPage) {
          // Also reset terms block when leaving legal so next time it opens hidden
          if (legalContent && !legalContent.classList.contains('hidden')) {
            legalContent.classList.add('hidden');
          }
          riskPage.classList.remove('hidden');
          if (kycStep) {
            kycStep.classList.add('hidden');
          }
          if (riskConsentStep) {
            riskConsentStep.classList.remove('hidden');
            riskConsentStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
          } else {
            riskPage.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }
      });

      witnessBackButton?.addEventListener('click', (event) => {
        event.preventDefault();
        // From witness details, go back to legal acknowledgements if present, else to risk
        if (legalPage && !legalPage.classList.contains('hidden')) {
          // Should not normally happen, but keep for safety
          legalPage.scrollIntoView({ behavior: 'smooth', block: 'start' });
          return;
        }

        if (legalPage) {
          witnessPage?.classList.add('hidden');
          // Ensure terms detail starts hidden when returning to legal page
          if (legalContent && !legalContent.classList.contains('hidden')) {
            legalContent.classList.add('hidden');
          }
          legalPage.classList.remove('hidden');
          legalPage.scrollIntoView({ behavior: 'smooth', block: 'start' });
          return;
        }

        if (riskPage) {
          witnessPage?.classList.add('hidden');
          riskPage.classList.remove('hidden');
          riskPage.scrollIntoView({ behavior: 'smooth', block: 'start' });
          return;
        }

        showFormStep('details');
      });

      if (riskConsentFilterInputs?.length) {
        riskConsentFilterInputs.forEach((chip) => {
          chip.addEventListener('change', () => {
            applyRiskConsentFilter();
          });
        });

        applyRiskConsentFilter();
      } else if (riskConsentSelectionMessage) {
        riskConsentSelectionMessage.classList.remove('hidden');
      }

      if (!shouldShowForm) {
        languageNext?.addEventListener('click', function () {
          languageCard?.classList.add('hidden');
          welcomeCard?.classList.remove('hidden');
          welcomeCard?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        welcomeBack?.addEventListener('click', function () {
          welcomeCard?.classList.add('hidden');
          formSection?.classList.add('hidden');
          languageCard?.classList.remove('hidden');
          languageCard?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        welcomeNext?.addEventListener('click', function () {
          welcomeCard?.classList.add('hidden');
          consentForCard?.classList.remove('hidden');
          consentForCard?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        consentForBack?.addEventListener('click', function () {
          consentForCard?.classList.add('hidden');
          identityCard?.classList.add('hidden');
          formSection?.classList.add('hidden');
          welcomeCard?.classList.remove('hidden');
          welcomeCard?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        consentForNext?.addEventListener('click', function () {
          // Reset geolocation flag to allow fresh request
          geolocationRequested = false;
          
          // Check if browser location access is available before proceeding to next step
          const clientLatitudeInput = document.getElementById('clientLatitudeInput');
          const clientLongitudeInput = document.getElementById('clientLongitudeInput');
          
          if (!clientLatitudeInput?.value || !clientLongitudeInput?.value) {
            // Show browser's native location permission popup
            if (navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(
                (position) => {
                  // Location obtained, populate fields and proceed
                  if (clientLatitudeInput) clientLatitudeInput.value = position.coords.latitude;
                  if (clientLongitudeInput) clientLongitudeInput.value = position.coords.longitude;
                  console.log('Location obtained:', position.coords);
                  
                  // Now proceed to next step
                  consentForCard?.classList.add('hidden');
                  identityCard?.classList.remove('hidden');
                  identityCard?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                },
                (error) => {
                  console.warn('Location access denied:', error);
                  alert('Please enable location access in your browser settings to continue. Location is required for consent verification.');
                }
              );
            } else {
              alert('Your browser does not support location access. Please use a modern browser.');
            }
            return;
          }
          
          // Location already available, proceed to next step
          consentForCard?.classList.add('hidden');
          identityCard?.classList.remove('hidden');
          identityCard?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        identityBack?.addEventListener('click', function () {
          identityCard?.classList.add('hidden');
          formSection?.classList.add('hidden');
          consentForCard?.classList.remove('hidden');
          consentForCard?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        identityNext?.addEventListener('click', function () {
          identityCard?.classList.add('hidden');
          formSection?.classList.remove('hidden');
          formSection?.scrollIntoView({ behavior: 'smooth', block: 'start' });
          showFormStep('details', { scroll: false });
          requestGeolocation();
          if (selectedConsentFor === 'relative') {
            relativeFields[0]?.focus();
          }
        });

        formBack?.addEventListener('click', function () {
          formSection?.classList.add('hidden');
          consentForCard?.classList.remove('hidden');
          consentForCard?.scrollIntoView({ behavior: 'smooth', block: 'start' });
          showFormStep('details', { scroll: false });
        });
      }

      signatureUploadInput?.addEventListener('change', (event) => {
        const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
        handleUploadFile(file);
      });

      thumbUploadInput?.addEventListener('change', (event) => {
        const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
        handleUploadFile(file, 'thumb');
      });

      idProofUploadInput?.addEventListener('change', (event) => {
        const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
        handleUploadFile(file, 'id_proof');
      });

      signatureCropConfirm?.addEventListener('click', applyCrop);

      const cancelCrop = () => {
        closeCropModal(true);
      };

      signatureCropCancel?.addEventListener('click', cancelCrop);
      signatureCropClose?.addEventListener('click', cancelCrop);

      signatureReCropButton?.addEventListener('click', () => {
        const file = signatureUploadInput?.files && signatureUploadInput.files[0] ? signatureUploadInput.files[0] : null;
        if (file && file.type.startsWith('image/')) {
          currentCropFile = file;
          openCropModal(file);
        } else {
          signatureUploadError?.classList.remove('hidden');
          if (signatureUploadError) {
            signatureUploadError.textContent = 'Please select an image file to crop.';
          }
        }
      });

      thumbReCropButton?.addEventListener('click', () => {
        const file = thumbUploadInput?.files && thumbUploadInput.files[0] ? thumbUploadInput.files[0] : null;
        if (file && file.type.startsWith('image/')) {
          handleUploadFile(file, 'thumb');
        } else {
          thumbUploadError?.classList.remove('hidden');
          thumbUploadError && (thumbUploadError.textContent = 'Please select an image file to crop again.');
        }
      });

      idProofReCropButton?.addEventListener('click', () => {
        const file = idProofUploadInput?.files && idProofUploadInput.files[0] ? idProofUploadInput.files[0] : null;
        if (file && file.type.startsWith('image/')) {
          handleUploadFile(file, 'id_proof');
        } else {
          idProofUploadError?.classList.remove('hidden');
          if (idProofUploadError) {
            idProofUploadError.textContent = 'Please select an image file to crop again.';
          }
        }
      });

      witnessUploadInput?.addEventListener('change', (event) => {
        const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
        handleUploadFile(file, 'witness');
      });

      witnessReCropButton?.addEventListener('click', () => {
        const file = witnessUploadInput?.files && witnessUploadInput.files[0] ? witnessUploadInput.files[0] : null;
        if (file && file.type.startsWith('image/')) {
          handleUploadFile(file, 'witness');
        } else {
          witnessUploadError?.classList.remove('hidden');
          witnessUploadError && (witnessUploadError.textContent = 'Please select an image file to crop again.');
        }
      });
    });
  </script>
</body>
</html>
