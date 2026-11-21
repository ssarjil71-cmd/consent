@extends('organization.layouts.app')

@section('content')
@php
  $sexOptions = [
    'male' => 'Male',
    'female' => 'Female',
    'other' => 'Other',
    'prefer_not_to_say' => 'Prefer not to say',
  ];
  $sexLabel = $consent->sex ? ($sexOptions[$consent->sex] ?? ucfirst(str_replace('_', ' ', $consent->sex))) : '—';
  $relativeName = $consent->consent_for === 'relative' ? ($consent->relative_name ?: '—') : '—';
  $relativeRelation = $consent->consent_for === 'relative' ? ($consent->relative_relation ?: '—') : '—';
@endphp

<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold">Consent Detail</h1>
      <p class="text-gray-600 text-sm">{{ $organization->name }}</p>
    </div>
    <a href="{{ route('org.consents.index') }}" class="px-3 py-2 rounded border bg-white hover:bg-gray-50 text-sm">Back</a>
  </div>

  <nav class="border-b border-gray-200">
    <ul class="-mb-px flex flex-wrap items-center gap-2 text-sm font-medium text-gray-600" role="tablist">
      <li>
        <button type="button" data-tab-target="details" class="tab-link inline-flex items-center gap-1 rounded-t-lg border border-gray-200 border-b-white bg-white px-3 py-2 text-gray-900 shadow-sm" aria-selected="true">
          Details
        </button>
      </li>
      <li>
        <button type="button" data-tab-target="history" class="tab-link inline-flex items-center gap-1 rounded-t-lg border border-transparent px-3 py-2 hover:border-gray-200 hover:bg-gray-50 hover:text-gray-900" aria-selected="false">
          History
        </button>
      </li>
      <li>
        <button type="button" data-tab-target="location" class="tab-link inline-flex items-center gap-1 rounded-t-lg border border-transparent px-3 py-2 hover:border-gray-200 hover:bg-gray-50 hover:text-gray-900" aria-selected="false">
          Location Map
        </button>
      </li>
      <li>
        <button type="button" data-tab-target="other" class="tab-link inline-flex items-center gap-1 rounded-t-lg border border-transparent px-3 py-2 hover:border-gray-200 hover:bg-gray-50 hover:text-gray-900" aria-selected="false">
          Other Details
        </button>
      </li>
      <li>
        <button type="button" data-tab-target="kyc" class="tab-link inline-flex items-center gap-1 rounded-t-lg border border-transparent px-3 py-2 hover:border-gray-200 hover:bg-gray-50 hover:text-gray-900" aria-selected="false">
          KYC Details
        </button>
      </li>
      <li>
        <button type="button" data-tab-target="witness" class="tab-link inline-flex items-center gap-1 rounded-t-lg border border-transparent px-3 py-2 hover:border-gray-200 hover:bg-gray-50 hover:text-gray-900" aria-selected="false">
          Witness Details
        </button>
      </li>
    </ul>
  </nav>

  <div data-tab-panel="details" class="tab-panel bg-white border border-gray-100 rounded-xl shadow-sm">
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <div class="text-xs text-gray-500">Name</div>
        <div class="font-medium text-gray-900">{{ $consent->name }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Email</div>
        <div class="font-medium text-gray-900">{{ $consent->email ?: '—' }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Phone</div>
        <div class="font-medium text-gray-900">{{ $consent->phone ?: '—' }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">OTP Code</div>
        <div class="font-medium text-gray-900">{{ $consent->otp_code ?: '—' }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">OTP Verified At</div>
        <div class="font-medium text-gray-900">
          {{ $consent->otp_verified_at ? $consent->otp_verified_at->timezone(config('app.timezone'))->format('d M Y, h:i A') : '—' }}
        </div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Age</div>
        <div class="font-medium text-gray-900">{{ $consent->age !== null ? $consent->age : '—' }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Sex</div>
        <div class="font-medium text-gray-900">{{ $sexLabel }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Consent For</div>
        <div class="font-medium text-gray-900">{{ ucfirst($consent->consent_for) }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Relative Name</div>
        <div class="font-medium text-gray-900">{{ $relativeName }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Relation</div>
        <div class="font-medium text-gray-900">{{ $relativeRelation }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Status</div>
        <span class="inline-flex px-2 py-1 rounded-full text-xs {{ $consent->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($consent->status === 'rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
          {{ ucfirst($consent->status) }}
        </span>
      </div>
      <div class="md:col-span-2">
        <div class="text-xs text-gray-500">Address</div>
        <div class="font-medium text-gray-900 whitespace-pre-wrap">{{ $consent->address ?: '—' }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Registration No.</div>
        <div class="font-medium text-gray-900">{{ $consent->registration_number ?: '—' }}</div>
      </div>
      
      <div class="md:col-span-2">
        <div class="text-xs text-gray-500">Notes</div>
        <div class="font-medium text-gray-900 whitespace-pre-wrap">{{ $consent->notes ?: '—' }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Submitted At</div>
        <div class="font-medium text-gray-900">{{ $consent->created_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</div>
      </div>
      
      @if($consent->status === 'approved' && $consent->person)
      <div class="md:col-span-2">
        <div class="text-xs text-gray-500">Assigned To</div>
        <div class="font-medium text-gray-900">
          {{ $consent->person->name }}
          @if($consent->person->email)
            <span class="text-gray-500 text-sm">({{ $consent->person->email }})</span>
          @endif
        </div>
      </div>
      @endif

      @if($consent->status === 'pending')
      <div class="md:col-span-2 border-t border-gray-100 pt-6 space-y-4">
        <h3 class="text-lg font-semibold text-gray-900">Approve Consent</h3>
        
        @if($organization->per_consent_amount > 0)
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
              </svg>
              <div class="text-sm text-blue-800">
                <strong>Payment Required:</strong> ₹{{ number_format($organization->per_consent_amount, 2) }} will be charged for this consent approval.
              </div>
            </div>
          </div>
        @endif

        <form action="{{ route('org.consents.approve', $consent) }}" method="POST" class="space-y-4" id="approveForm">
          @csrf
          
          @if($persons->isNotEmpty())
            <div>
              <label for="person_id" class="block text-sm font-medium text-gray-700 mb-1">Assign to Person (Optional)</label>
              <select name="person_id" id="person_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- Select a person --</option>
                @foreach($persons as $person)
                  <option value="{{ $person->id }}" {{ old('person_id') == $person->id ? 'selected' : '' }}>
                    {{ $person->name }}
                    @if($person->email) ({{ $person->email }}) @endif
                  </option>
                @endforeach
              </select>
              @error('person_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
          @endif

          @if($organization->per_consent_amount > 0)
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method</label>
              <div class="space-y-3">
                <div class="flex items-center">
                  <input type="radio" name="payment_method" value="balance" id="payment_balance" class="mr-3" checked>
                  <label for="payment_balance" class="flex-1">
                    <div class="font-medium text-gray-900">Debit from Current Balance</div>
                    <div class="text-sm text-gray-500">Current Balance: ₹{{ number_format($organization->transaction_amount, 2) }}</div>
                  </label>
                </div>
                
                <div class="flex items-center">
                  <input type="radio" name="payment_method" value="razorpay" id="payment_azorpay" class="mr-3">
                  <label for="payment_azorpay" class="flex-1">
                    <div class="font-medium text-gray-900">Pay by {{ $organization->consent_label ?? 'Consent' }}</div>
                    <div class="text-sm text-gray-500">Pay ₹{{ number_format($organization->per_consent_amount, 2) }} via Razorpay</div>
                  </label>
                </div>
              </div>
              @error('payment_method')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
          @endif

          <div class="flex items-center gap-3">
            @if($organization->per_consent_amount > 0)
              <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" id="approveBtn">Approve Consent</button>
            @else
              <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Approve Consent</button>
            @endif
            <a href="{{ route('org.consents.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Back to List</a>
          </div>
        </form>
      </div>
      @else
      <div class="md:col-span-2 border-t border-gray-100 pt-6 flex items-center gap-3">
        <a href="{{ route('org.consents.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Back to List</a>
        <a href="{{ route('org.consents.pdf', $consent) }}" target="_blank" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">View PDF</a>
      </div>
      @endif
    </div>
  </div>

  <div data-tab-panel="location" class="tab-panel hidden bg-white border border-gray-100 rounded-xl shadow-sm">
    <div class="p-6 space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <div class="text-xs text-gray-500 uppercase tracking-wide">Client IP</div>
          <div class="font-medium text-gray-900">{{ $consent->client_ip ?: '—' }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase tracking-wide">Approximate Location</div>
          <div class="font-medium text-gray-900">{{ $consent->client_location ?: '—' }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase tracking-wide">Latitude</div>
          <div class="font-medium text-gray-900">{{ optional($consent->client_latitude, fn($value) => number_format($value, 6)) ?: '—' }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase tracking-wide">Longitude</div>
          <div class="font-medium text-gray-900">{{ optional($consent->client_longitude, fn($value) => number_format($value, 6)) ?: '—' }}</div>
        </div>
      </div>

      @php
        $hasCoordinates = $consent->client_latitude !== null && $consent->client_longitude !== null;
        $locationQuery = $consent->client_location ?: ($consent->address ?: null);
      @endphp

      <div
        id="locationMap"
        data-lat="{{ $hasCoordinates ? $consent->client_latitude : '' }}"
        data-lng="{{ $hasCoordinates ? $consent->client_longitude : '' }}"
        data-query="{{ !$hasCoordinates && $locationQuery ? e($locationQuery) : '' }}"
        class="relative hidden h-72 w-full overflow-hidden rounded-xl border border-gray-200"
      ></div>

      <div id="locationMapFallback" class="hidden rounded-lg border border-dashed border-gray-200 bg-gray-50 p-6 text-sm text-gray-500">
        @if($hasCoordinates)
          <div class="mt-2 aspect-video w-full overflow-hidden rounded-lg border border-gray-200 bg-white">
            <iframe
              src="https://www.google.com/maps?q={{ $consent->client_latitude }},{{ $consent->client_longitude }}&hl=en&z=14&output=embed"
              width="100%"
              height="100%"
              style="border:0;"
              allowfullscreen
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
          </div>
        @else
          No map data available for this consent.
        @endif
      </div>
    </div>
  </div>

  <div data-tab-panel="history" class="tab-panel hidden"></div>
  <div data-tab-panel="other" class="tab-panel hidden bg-white border border-gray-100 rounded-xl shadow-sm">
    <div class="p-6 space-y-8">

      @php
        $signaturePath = $consent->signature_upload_path;
        $signatureUrl = null;
        if ($signaturePath) {
          if (\Illuminate\Support\Str::startsWith($signaturePath, ['http://', 'https://'])) {
            $signatureUrl = $signaturePath;
          } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($signaturePath)) {
            $signatureUrl = \Illuminate\Support\Facades\Storage::url($signaturePath);
          }
        }

        $thumbPath = $consent->thumb_upload_path;
        $thumbUrl = null;
        if ($thumbPath) {
          if (\Illuminate\Support\Str::startsWith($thumbPath, ['http://', 'https://'])) {
            $thumbUrl = $thumbPath;
          } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($thumbPath)) {
            $thumbUrl = \Illuminate\Support\Facades\Storage::url($thumbPath);
          }
        }

        $witnessPath = $consent->witness_signature_path;
        $witnessUrl = null;
        if ($witnessPath) {
          if (\Illuminate\Support\Str::startsWith($witnessPath, ['http://', 'https://'])) {
            $witnessUrl = $witnessPath;
          } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($witnessPath)) {
            $witnessUrl = \Illuminate\Support\Facades\Storage::url($witnessPath);
          }
        }

        $selfiePath = $consent->selfie_upload_path;
        $selfieUrl = null;
        if ($selfiePath) {
          if (\Illuminate\Support\Str::startsWith($selfiePath, ['http://', 'https://'])) {
            $selfieUrl = $selfiePath;
          } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($selfiePath)) {
            $selfieUrl = \Illuminate\Support\Facades\Storage::url($selfiePath);
          }
        }

        $idProofPath = $consent->id_proof_upload_path;
        $idProofUrl = null;
        if ($idProofPath) {
          if (\Illuminate\Support\Str::startsWith($idProofPath, ['http://', 'https://'])) {
            $idProofUrl = $idProofPath;
          } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($idProofPath)) {
            $idProofUrl = \Illuminate\Support\Facades\Storage::url($idProofPath);
          }
        }

        $thumbVideoPath = $consent->thumb_video_path;
        $thumbVideoUrl = null;
        if ($thumbVideoPath) {
          if (\Illuminate\Support\Str::startsWith($thumbVideoPath, ['http://', 'https://'])) {
            $thumbVideoUrl = $thumbVideoPath;
          } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($thumbVideoPath)) {
            $thumbVideoUrl = \Illuminate\Support\Facades\Storage::url($thumbVideoPath);
          }
        }
      @endphp

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
          <div class="flex items-start justify-between border-b border-gray-100 px-5 py-4">
            <div>
              <h4 class="text-sm font-semibold text-gray-900">Signature</h4>
            </div>
          </div>

          <div class="space-y-4 px-5 py-4">
            @if($signatureUrl)
              @if(\Illuminate\Support\Str::endsWith($signaturePath, ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                <div class="overflow-hidden rounded-xl border border-gray-100 bg-gray-50 p-3">
                  <img src="{{ $signatureUrl }}" alt="Signature preview" class="mx-auto block h-48 w-full max-w-md object-contain">
                </div>
              @else
                <p class="text-sm leading-relaxed text-gray-600">Signature was uploaded as a document.</p>
              @endif
            @else
              <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
                No signature file found for this consent.
              </div>
            @endif
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
          <div class="flex items-start justify-between border-b border-gray-100 px-5 py-4">
            <div>
              <h4 class="text-sm font-semibold text-gray-900">Thumb Impression</h4>
            </div>
            @if($thumbUrl)
              <a href="{{ $thumbUrl }}" target="_blank" class="inline-flex items-center gap-2 rounded-full border border-gray-300 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:bg-gray-100">
                Download
              </a>
            @endif
          </div>

          <div class="px-5 py-4">
            @if($thumbUrl)
              <div class="overflow-hidden rounded-xl border border-gray-100 bg-gray-50 p-3">
                <img src="{{ $thumbUrl }}" alt="Thumb impression preview" class="mx-auto block h-48 w-full max-w-md object-contain">
              </div>
            @else
              <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
                No thumb impression file found for this consent.
              </div>
            @endif
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
          <div class="flex items-start justify-between border-b border-gray-100 px-5 py-4">
            <div>
              <h4 class="text-sm font-semibold text-gray-900">Selfie</h4>
            </div>
            @if($selfieUrl)
              <a href="{{ $selfieUrl }}" target="_blank" class="inline-flex items-center gap-2 rounded-full border border-gray-300 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:bg-gray-100">
                Download
              </a>
            @endif
          </div>

          <div class="px-5 py-4">
            @if($selfieUrl)
              <div class="overflow-hidden rounded-xl border border-gray-100 bg-gray-50 p-3">
                <img src="{{ $selfieUrl }}" alt="Selfie preview" class="mx-auto block h-48 w-full max-w-md object-contain">
              </div>
            @else
              <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
                No selfie file found for this consent.
              </div>
            @endif
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
          <div class="flex items-start justify-between border-b border-gray-100 px-5 py-4">
            <div>
              <h4 class="text-sm font-semibold text-gray-900">ID Proof</h4>
            </div>
            @if($idProofUrl)
              <a href="{{ $idProofUrl }}" target="_blank" class="inline-flex items-center gap-2 rounded-full border border-gray-300 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:bg-gray-100">
                Download
              </a>
            @endif
          </div>

          <div class="px-5 py-4">
            @if($idProofUrl)
              @if(\Illuminate\Support\Str::endsWith($idProofPath, ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                <div class="overflow-hidden rounded-xl border border-gray-100 bg-gray-50 p-3">
                  <img src="{{ $idProofUrl }}" alt="ID proof preview" class="mx-auto block h-48 w-full max-w-md object-contain">
                </div>
              @else
                <p class="text-sm leading-relaxed text-gray-600">ID proof was uploaded as a document.</p>
              @endif
            @else
              <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
                No ID proof file found for this consent.
              </div>
            @endif
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
          <div class="flex items-start justify-between border-b border-gray-100 px-5 py-4">
            <div>
              <h4 class="text-sm font-semibold text-gray-900">Video</h4>
            </div>
            @if($thumbVideoUrl)
              <a href="{{ $thumbVideoUrl }}" target="_blank" class="inline-flex items-center gap-2 rounded-full border border-gray-300 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:bg-gray-100">
                Download
              </a>
            @endif
          </div>

          <div class="px-5 py-4">
            @if($thumbVideoUrl)
              <video controls class="mx-auto block w-full max-w-md rounded-xl border border-gray-100 bg-black">
                <source src="{{ $thumbVideoUrl }}" type="video/mp4">
                Your browser does not support the video tag.
              </video>
            @else
              <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
                No thumb video file found for this consent.
              </div>
            @endif
          </div>
        </div>

      </div>
    </div>
  </div>

  <div data-tab-panel="kyc" class="tab-panel hidden bg-white border border-gray-100 rounded-xl shadow-sm">
    <div class="p-6 space-y-6">
      <div>
        <h3 class="text-lg font-semibold text-gray-900">KYC Verification Details</h3>
        <p class="text-sm text-gray-500">Know Your Customer verification information captured during consent.</p>
      </div>

      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
          <div class="text-xs uppercase tracking-wide text-gray-500">Document Type</div>
          <div class="mt-1 text-sm font-medium text-gray-900">
            @if($consent->kyc_type)
              {{ ucfirst(str_replace('_', ' ', $consent->kyc_type)) }}
            @else
              —
            @endif
          </div>
        </div>
        <div>
          <div class="text-xs uppercase tracking-wide text-gray-500">Document Number</div>
          <div class="mt-1 text-sm font-medium text-gray-900">{{ $consent->kyc_number ?: '—' }}</div>
        </div>
        <div>
          <div class="text-xs uppercase tracking-wide text-gray-500">Verification Method</div>
          <div class="mt-1 text-sm font-medium text-gray-900">
            @if($consent->kyc_verification_method)
              {{ ucfirst($consent->kyc_verification_method) === 'Number' ? 'Document Number' : 'Document Image Upload' }}
            @else
              —
            @endif
          </div>
        </div>
        <div>
          <div class="text-xs uppercase tracking-wide text-gray-500">KYC Verified At</div>
          <div class="mt-1 text-sm font-medium text-gray-900">
            {{ $consent->kyc_verified_at ? $consent->kyc_verified_at->timezone(config('app.timezone'))->format('d M Y, h:i A') : '—' }}
          </div>
        </div>
      </div>

      @if(!$consent->kyc_type)
        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
          No KYC verification was performed for this consent.
        </div>
      @endif
    </div>
  </div>

  <div data-tab-panel="witness" class="tab-panel hidden bg-white border border-gray-100 rounded-xl shadow-sm">
    <div class="p-6 space-y-6">
      <div>
        <h3 class="text-lg font-semibold text-gray-900">Witness Details</h3>
        <p class="text-sm text-gray-500">Information provided by the witness during consent.</p>
      </div>

      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
          <div class="text-xs uppercase tracking-wide text-gray-500">Witness Name</div>
          <div class="mt-1 text-sm font-medium text-gray-900">{{ $consent->witness_name ?: '—' }}</div>
        </div>
        <div>
          <div class="text-xs uppercase tracking-wide text-gray-500">Witness Phone</div>
          <div class="mt-1 text-sm font-medium text-gray-900">{{ $consent->witness_phone ?: '—' }}</div>
        </div>
        <div>
          <div class="text-xs uppercase tracking-wide text-gray-500">Witness Relation</div>
          <div class="mt-1 text-sm font-medium text-gray-900">{{ $consent->witness_relation ?: '—' }}</div>
        </div>
        <div>
          <div class="text-xs uppercase tracking-wide text-gray-500">Witness Email</div>
          <div class="mt-1 text-sm font-medium text-gray-900">{{ $consent->witness_email ?: '—' }}</div>
        </div>
      </div>

      <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
        <div class="flex items-center justify-between">
          <div>
            <h4 class="text-sm font-semibold text-gray-900">Witness Signature</h4>
            <p class="text-xs text-gray-500">Captured as supporting evidence.</p>
          </div>
          @if($witnessUrl)
            <a href="{{ $witnessUrl }}" target="_blank" class="inline-flex items-center gap-2 rounded-full border border-gray-300 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:bg-gray-100">
              Download
            </a>
          @endif
        </div>

        <div class="mt-4">
          @if($witnessUrl)
            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white p-3">
              <img src="{{ $witnessUrl }}" alt="Witness signature preview" class="mx-auto block h-48 w-full max-w-md object-contain">
            </div>
          @else
            <div class="rounded-xl border border-dashed border-gray-300 bg-white px-4 py-10 text-center text-sm text-gray-500">
              No witness signature available for this consent.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@once
  @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  @endpush
  
  @if($consent->status === 'pending' && $organization->per_consent_amount > 0)
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  @endif
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-o8EeYJ8notHprkuCBRqkzj8+ie3UYjit31ax4ySAwxg=" crossorigin=""></script>
@endonce
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabPanels = document.querySelectorAll('.tab-panel');

    const activateTab = (target) => {
      tabLinks.forEach((link) => {
        const isActive = link.getAttribute('data-tab-target') === target;
        link.setAttribute('aria-selected', isActive ? 'true' : 'false');
        link.classList.toggle('border-gray-200', isActive);
        link.classList.toggle('border-transparent', !isActive);
        link.classList.toggle('border-b-white', isActive);
        link.classList.toggle('bg-white', isActive);
        link.classList.toggle('text-gray-900', isActive);
        link.classList.toggle('shadow-sm', isActive);
      });

      tabPanels.forEach((panel) => {
        const isActive = panel.getAttribute('data-tab-panel') === target;
        panel.classList.toggle('hidden', !isActive);
      });
    };

    tabLinks.forEach((link) => {
      link.addEventListener('click', () => {
        const target = link.getAttribute('data-tab-target');
        activateTab(target);
      });
    });

    const mapContainer = document.getElementById('locationMap');
    const fallback = document.getElementById('locationMapFallback');
    let mapInitialized = false;

    const showFallback = () => {
      if (mapContainer) {
        mapContainer.classList.add('hidden');
      }
      if (fallback) {
        fallback.classList.remove('hidden');
      }
    };

    const initMap = (latitude, longitude, label) => {
      if (!mapContainer) {
        showFallback();
        return;
      }

      // If Leaflet failed to load, show fallback instead of a blank area
      if (typeof L === 'undefined') {
        showFallback();
        return;
      }

      mapContainer.classList.remove('hidden');
      if (fallback) {
        fallback.classList.add('hidden');
      }

      try {
        const map = L.map(mapContainer).setView([latitude, longitude], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        L.marker([latitude, longitude]).addTo(map).bindPopup(label || 'Captured location').openPopup();
        mapInitialized = true;
      } catch (error) {
        console.error('Unable to initialize map', error);
        showFallback();
      }
    };

    const prepareMap = () => {
      if (!mapContainer || mapInitialized) {
        if (!mapContainer) {
          showFallback();
        }
        return;
      }

      const lat = parseFloat(mapContainer.dataset.lat);
      const lng = parseFloat(mapContainer.dataset.lng);
      const query = mapContainer.dataset.query;

      if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
        initMap(lat, lng, query || 'Captured location');
      } else if (query) {
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
          .then((response) => response.json())
          .then((results) => {
            if (Array.isArray(results) && results.length > 0) {
              const match = results[0];
              const latitude = parseFloat(match.lat);
              const longitude = parseFloat(match.lon);
              if (!Number.isNaN(latitude) && !Number.isNaN(longitude)) {
                initMap(latitude, longitude, match.display_name);
              } else {
                showFallback();
              }
            } else {
              showFallback();
            }
          })
          .catch((error) => {
            console.error('Geocoding failed', error);
            showFallback();
          });
      } else {
        showFallback();
      }
    };

    tabLinks.forEach((link) => {
      link.addEventListener('click', () => {
        const target = link.getAttribute('data-tab-target');
        activateTab(target);
        if (target === 'location') {
          setTimeout(prepareMap, 50);
        }
      });
    });

    if (document.querySelector('.tab-link[aria-selected="true"][data-tab-target="location"]')) {
      prepareMap();
    }
  });

  // Razorpay Payment Integration
  @if($consent->status === 'pending' && $organization->per_consent_amount > 0)
    document.addEventListener('DOMContentLoaded', function() {
      const approveForm = document.getElementById('approveForm');
      const approveBtn = document.getElementById('approveBtn');
      const paymentBalance = document.getElementById('payment_balance');
      const paymentRazorpay = document.getElementById('payment_azorpay');
      
      if (approveForm && approveBtn) {
        approveForm.addEventListener('submit', function(e) {
          const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
          
          if (selectedPayment && selectedPayment.value === 'razorpay') {
            e.preventDefault();
            openRazorpay();
          }
        });
      }
      
      function openRazorpay() {
        const options = {
          key: '{{ config("services.razorpay.key") }}',
          amount: {{ $organization->per_consent_amount * 100 }}, // Convert to paise
          currency: 'INR',
          name: '{{ $organization->name }}',
          description: 'Payment for consent approval - Consent #{{ $consent->id }}',
          image: '{{ asset("logo.png") }}',
          handler: function(response) {
            // Add razorpay payment ID to form and submit
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'razorpay_payment_id';
            hiddenInput.value = response.razorpay_payment_id;
            approveForm.appendChild(hiddenInput);
            
            approveForm.submit();
          },
          modal: {
            ondismiss: function() {
              // User closed the modal without payment
              console.log('Payment cancelled');
            }
          },
          prefill: {
            name: '{{ Auth::guard('organization')->user()->name }}',
            email: '{{ Auth::guard('organization')->user()->email }}',
            contact: '{{ Auth::guard('organization')->user()->phone ?? "" }}'
          },
          theme: {
            color: '#10b981'
          }
        };
        
        const rzp = new Razorpay(options);
        rzp.open();
      }
    });
  @endif
</script>
@endpush
