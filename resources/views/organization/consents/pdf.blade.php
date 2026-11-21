<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <style>
    @font-face {
      font-family: 'Mukta';
      src: url('https://fonts.googleapis.com/css2?family=Mukta:wght@400;600&display=swap');
    }
    body { 
      font-family: 'Roboto', Arial, sans-serif; 
      font-size: 12px; 
      color: #000000 !important;
      line-height: 1.5;
    }
    .hindi, .marathi {
      font-weight: 600;
      letter-spacing: 0.3px;
    }
    .header { border-bottom:1px solid #ddd; padding-bottom:8px; margin-bottom:16px; }
    .title { font-size: 18px; font-weight: 700; }
    .muted { color:#000000 !important; opacity: 0.8; }
    .section { margin-bottom: 14px; }
    .label { font-size: 13px; font-weight: 700; color:#000000 !important; text-transform: uppercase; }
    .value { font-size: 13px; font-weight: 600; color:#000000 !important; }
    .section-heading {
      font-size: 16px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      color: #000000 !important;
      margin-bottom: 8px;
    }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 20px; }
    .badge { display:inline-block; padding:2px 8px; border-radius: 9999px; font-size: 10px; }
    .badge-approved { background:#ecfdf5; color:#047857 }
    .badge-pending { background:#fffbeb; color:#92400e }
    .badge-rejected{ background:#fff1f2; color:#be123c }
    .lang-header { font-size:14px; font-weight:700; margin-bottom:10px; }
    .page-break { page-break-after: always; }
  </style>
</head>
<body>
  @php $count = count($langs ?? ['en']); @endphp
  @foreach(($langs ?? ['en']) as $i => $code)
    @php 
      $t = $translationsMap[$code] ?? $translationsMap['en'];
      $langClass = $code === 'hi' ? 'hindi' : ($code === 'mr' ? 'marathi' : '');
      $riskText = match ($code) {
        'hi' => $consent->risk_consent_text_hi,
        'mr' => $consent->risk_consent_text_mr,
        default => $consent->risk_consent_text_en ?? $consent->risk_consent_text,
      };

      if (empty($riskText) && $code !== 'en') {
        $riskText = $consent->risk_consent_text_en ?? $consent->risk_consent_text;
      }

      $trim = function ($value) {
        if (!is_string($value)) {
          return null;
        }
        $value = trim($value);
        return $value === '' ? null : $value;
      };

      $toClauses = function ($text) use ($trim) {
        if (!$text) {
          return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $text))
          ->map(fn ($line) => $trim($line))
          ->filter()
          ->values()
          ->all();
      };

      $termsText = match ($code) {
        'hi' => $trim($consent->terms_text_hi),
        'mr' => $trim($consent->terms_text_mr),
        default => $trim($consent->terms_text_en),
      };
      if (!$termsText) {
        $termsText = $trim($consent->terms_text);
      }

      $liabilityText = match ($code) {
        'hi' => $trim($consent->liability_release_text_hi),
        'mr' => $trim($consent->liability_release_text_mr),
        default => $trim($consent->liability_release_text_en),
      };
      if (!$liabilityText) {
        $liabilityText = $trim($consent->liability_release_text);
      }

      $riskAcknowledgementText = match ($code) {
        'hi' => $trim($consent->risk_acknowledgement_text_hi),
        'mr' => $trim($consent->risk_acknowledgement_text_mr),
        default => $trim($consent->risk_acknowledgement_text_en),
      };
      if (!$riskAcknowledgementText) {
        $riskAcknowledgementText = $trim($consent->risk_acknowledgement_text);
      }

      $termsClauses = $toClauses($termsText);
      $liabilityClauses = $toClauses($liabilityText);
      $riskAcknowledgementClauses = $toClauses($riskAcknowledgementText);
      $riskClauses = $toClauses($riskText);
    @endphp
    <div class="{{ $langClass }}">
    <div class="header">
      <div class="title">{{ $t['consent'] }} #{{ $consent->id }}</div>
      <div class="muted">{{ $organization->name }} • {{ $t['generated'] }} {{ now()->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</div>
    </div>

    @php
      $selfieImagePath = null;
      $needsRotation = false;
      if ($consent->selfie_upload_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($consent->selfie_upload_path)) {
          $selfieImagePath = \Illuminate\Support\Facades\Storage::disk('public')->path($consent->selfie_upload_path);
          
          // Check if image needs rotation by examining dimensions
          if (function_exists('getimagesize')) {
              $imageInfo = @getimagesize($selfieImagePath);
              if ($imageInfo && $imageInfo[0] > $imageInfo[1]) {
                  // Width > Height suggests landscape orientation (likely needs rotation)
                  $needsRotation = true;
              }
          }
      }
      $badgeClass = $consent->status === 'approved' ? 'badge-approved' : ($consent->status === 'rejected' ? 'badge-rejected' : 'badge-pending');
    @endphp

    <div class="section" style="margin-bottom:14px;">
  <table width="100%" cellspacing="0" cellpadding="6">
    <tr>
      <td width="70%" valign="top">
        <div class="section grid">

          <div style="margin-bottom:10px;">
            <div class="label">{{ $t['name'] }}</div>
            <div class="value" style="margin-top:4px;">
              {{ $consent->name }}
            </div>
          </div>

          <div style="margin-bottom:10px;">
            <div class="label">{{ $t['email'] }}</div>
            <div class="value" style="margin-top:4px;">
              {{ $consent->email ?: '—' }}
            </div>
          </div>

          <div style="margin-bottom:10px;">
            <div class="label">{{ $t['phone'] }}</div>
            <div class="value" style="margin-top:4px;">
              {{ $consent->phone ?: '—' }}
            </div>
          </div>

          <div style="margin-bottom:10px;">
            <div class="label">{{ $t['otp_code'] ?? 'OTP Code' }}</div>
            <div class="value" style="margin-top:4px;">
              {{ $consent->otp_code ?: '—' }}
            </div>
          </div>

          <div style="margin-bottom:10px;">
            <div class="label">{{ $t['otp_verified_at'] ?? 'OTP Verified At' }}</div>
            <div class="value" style="margin-top:4px;">
              @if($consent->otp_verified_at)
                {{ $consent->otp_verified_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }}
              @else
                —
              @endif
            </div>
          </div>

          @if($consent->kyc_type || $consent->kyc_number || $consent->kyc_verification_method)

          <div style="margin-bottom:10px;">
            <div class="label">{{ $t['kyc_document_type'] ?? 'KYC Document Type' }}</div>
            <div class="value" style="margin-top:4px;">
              {{ $consent->kyc_type ? ucfirst(str_replace('_', ' ', $consent->kyc_type)) : '—' }}
            </div>
          </div>

          <div style="margin-bottom:10px;">
            <div class="label">{{ $t['kyc_document_number'] ?? 'Document Number' }}</div>
            <div class="value" style="margin-top:4px;">
              {{ $consent->kyc_number ?: '—' }}
            </div>
          </div>

          <div style="margin-bottom:10px;">
            <div class="label">{{ $t['kyc_verification_method'] ?? 'Verification Method' }}</div>
            <div class="value" style="margin-top:4px;">
              {{ $consent->kyc_verification_method
                ? (ucfirst($consent->kyc_verification_method) === 'Number'
                    ? 'Document Number'
                    : 'Document Image Upload')
                : '—' }}
            </div>
          </div>

          <div style="margin-bottom:10px;">
            <div class="label">{{ $t['kyc_verified_at'] ?? 'KYC Verified At' }}</div>
            <div class="value" style="margin-top:4px;">
              @if($consent->kyc_verified_at)
                {{ $consent->kyc_verified_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }}
              @else
                —
              @endif
            </div>
          </div>

          @endif

          <div style="margin-bottom:10px;">
            <div class="label">{{ $t['status'] }}</div>
            <div class="value" style="margin-top:4px;">
              <span class="badge {{ $badgeClass }}">{{ ucfirst($consent->status) }}</span>
            </div>
          </div>

          <div style="margin-bottom:10px;">
            <div class="label">{{ $t['submitted'] }}</div>
            <div class="value" style="margin-top:4px;">
              {{ $consent->created_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }}
            </div>
          </div>

          <div style="margin-bottom:10px;">
            <div class="label">{{ $t['updated'] }}</div>
            <div class="value" style="margin-top:4px;">
              {{ $consent->updated_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }}
            </div>
          </div>

        </div>
      </td>

      @if($selfieImagePath)
      <td width="30%" valign="top" align="right">
        <img
          src="{{ $selfieImagePath }}"
          style="max-width:100%; max-height:120px; border:1px solid #dddddd; margin-top:4px;
          @if($needsRotation) transform: rotate(270deg); @endif"
        />
      </td>
      @endif

    </tr>
  </table>
</div>


    @if($consent->notes)
    <div class="section">
      <div class="label">{{ $t['notes'] }}</div>
      <div class="value" style="white-space: pre-wrap;">{{ $consent->notes }}</div>
    </div>
    @endif

    @if(!empty($termsText))
    <div class="section">
      <div class="section-heading">{{ $t['terms_title'] ?? 'Terms & Conditions' }}</div>
      @if(count($termsClauses))
        <ol style="margin:6px 0 0 18px;">
          @foreach($termsClauses as $clause)
            <li style="font-size:13px; font-weight:600; color:#000000; margin-bottom:4px;">{{ $clause }}</li>
          @endforeach
        </ol>
      @else
        <div class="value" style="white-space: pre-wrap;">
          {{ $termsText }}
        </div>
      @endif
    </div>
    @endif

    @if(!empty($liabilityText))
    <div class="section">
      <div class="section-heading">{{ $t['liability_title'] ?? 'Liability Release' }}</div>
      @if(count($liabilityClauses))
        <ol style="margin:6px 0 0 18px;">
          @foreach($liabilityClauses as $clause)
            <li style="font-size:13px; font-weight:600; color:#000000; margin-bottom:4px;">{{ $clause }}</li>
          @endforeach
        </ol>
      @else
        <div class="value" style="white-space: pre-wrap;">
          {{ $liabilityText }}
        </div>
      @endif
    </div>
    @endif

    @if(!empty($riskText))
    <div class="section">
      <div class="section-heading">Risk Consent</div>
      @if(count($riskClauses))
        @php
          $inList = false;
        @endphp
        @foreach($riskClauses as $clause)
          @php
            $raw = trim($clause);
            // Skip pure separators like ---
            if ($raw === '---') { continue; }
            $isHeading = false;
            $headingText = $raw;
            if ($raw !== '' && $raw[0] === '#') {
              $isHeading = true;
              $headingText = ltrim(preg_replace('/^#+\s*/', '', $raw));
            }
          @endphp

          @if($isHeading)
            @if($inList)
              </ol>
              @php $inList = false; @endphp
            @endif
            <div class="section-heading" style="margin-top:6px;">{{ $headingText }}</div>
          @else
            @if(!$inList)
              @php $inList = true; @endphp
              <ol style="margin:6px 0 0 18px;">
            @endif
            <li style="font-size:13px; font-weight:600; color:#000000; margin-bottom:4px;">{{ $clause }}</li>
          @endif
        @endforeach
        @if($inList)
          </ol>
        @endif
      @else
        <div class="value" style="white-space: pre-wrap;">{{ $riskText }}</div>
      @endif
    </div>
    @endif

    @if(!empty($riskAcknowledgementText) || !is_null($consent->risk_acknowledged))
    <div class="section">
      <div class="section-heading">{{ $t['risk_acknowledgement_title'] ?? 'Risk Acknowledgement' }}</div>
      @if(count($riskAcknowledgementClauses))
        <ol style="margin:6px 0 0 18px;">
          @foreach($riskAcknowledgementClauses as $clause)
            <li style="font-size:13px; font-weight:600; color:#000000; margin-bottom:4px;">{{ $clause }}</li>
          @endforeach
        </ol>
      @elseif(!empty($riskAcknowledgementText))
        <div class="value" style="white-space: pre-wrap;">{{ $riskAcknowledgementText }}</div>
      @endif
    </div>
    @endif

    @php
      $signatureImagePath = null;
      if ($consent->signature_upload_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($consent->signature_upload_path)) {
        $signatureImagePath = \Illuminate\Support\Facades\Storage::disk('public')->path($consent->signature_upload_path);
      }

      $thumbImagePath = null;
      if ($consent->thumb_upload_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($consent->thumb_upload_path)) {
        $thumbImagePath = \Illuminate\Support\Facades\Storage::disk('public')->path($consent->thumb_upload_path);
      }

      $witnessImagePath = null;
      if ($consent->witness_signature_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($consent->witness_signature_path)) {
        $witnessImagePath = \Illuminate\Support\Facades\Storage::disk('public')->path($consent->witness_signature_path);
      }

      $idProofImagePath = null;
      if ($consent->id_proof_upload_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($consent->id_proof_upload_path)) {
        $idProofImagePath = \Illuminate\Support\Facades\Storage::disk('public')->path($consent->id_proof_upload_path);
      }

      // Build entity label from organization's consent label (e.g. Patient / Member)
      $entityLabel = trim((string)($organization->consent_label ?? ''));
      if ($entityLabel === '') {
        $slug = \Illuminate\Support\Str::slug(strtolower($organization->type->slug ?? $organization->type->name ?? ''));
        $entityLabel = match ($slug) {
          'hospital' => 'Patient',
          'yoga' => 'Participant',
          'zumba' => 'Member',
          'wellness' => 'Client',
          default => 'Member',
        };
      }

      $idProofLabel = $entityLabel . ' ID Proof';

      $subjectName = $consent->name;
      if ($consent->consent_for === 'relative' && $consent->relative_name) {
        $subjectName = $consent->relative_name;
      }

      // Labels like "Patient Signature" and "Patient Thumb"
      if ($consent->consent_for === 'relative') {
        $signatureLabel = $entityLabel . ' Signature (Relative)';
        $thumbLabel = $entityLabel . ' Thumb (Relative)';
      } else {
        $signatureLabel = $entityLabel . ' Signature';
        $thumbLabel = $entityLabel . ' Thumb';
      }

      $witnessName = $consent->witness_name;
    @endphp

    @if($idProofImagePath || $signatureImagePath || $thumbImagePath || $witnessImagePath)
    <div class="section">
      @if($idProofImagePath || $signatureImagePath || $thumbImagePath)
        <table width="100%" cellspacing="0" cellpadding="4">
          <tr>
            @if($idProofImagePath)
              <td width="50%" valign="top">
                <div style="margin-bottom:10px;">
                  <div class="label">{{ $idProofLabel }}</div>
                  <img src="{{ $idProofImagePath }}" style="max-width:100%; max-height:160px; border:1px solid #dddddd; margin-top:4px;" />
                </div>
              </td>
            @endif
            <td valign="top">
              @if($signatureImagePath)
                <div style="margin-bottom:8px;">
                  @if($subjectName)
                    <div class="label">
                      {{ $signatureLabel }}: <span class="value" style="font-size:13px; font-weight:600;">{{ $subjectName }}</span>
                      <img src="{{ $signatureImagePath }}" style="max-height:60px; margin-left:6px; vertical-align:middle;" />
                    </div>
                  @else
                    <div class="label">{{ $signatureLabel }}</div>
                    <img src="{{ $signatureImagePath }}" style="max-width:100%; max-height:80px; margin-top:4px;" />
                  @endif
                </div>
              @endif

              @if($thumbImagePath)
                <div style="margin-bottom:8px;">
                  @if($subjectName)
                    <div class="label">
                      {{ $thumbLabel }}: <span class="value" style="font-size:13px; font-weight:600;">{{ $subjectName }}</span>
                      <img src="{{ $thumbImagePath }}" style="max-height:60px; margin-left:6px; vertical-align:middle;" />
                    </div>
                  @else
                    <div class="label">{{ $thumbLabel }}</div>
                    <img src="{{ $thumbImagePath }}" style="max-width:100%; max-height:80px; margin-top:4px;" />
                  @endif
                </div>
              @endif
            </td>
          </tr>
        </table>
      @endif

      @if($witnessImagePath)
        <div style="margin-top:8px;">
          <div class="label">Witness Details</div>
          <div style="font-size:11px; margin-top:2px;">
            <div><strong>Name:</strong> {{ $consent->witness_name ?: '—' }}</div>
            <div><strong>Phone:</strong> {{ $consent->witness_phone ?: '—' }}</div>
            <div><strong>Relation:</strong> {{ $consent->witness_relation ?: '—' }}</div>
            <div><strong>Email:</strong> {{ $consent->witness_email ?: '—' }}</div>
          </div>
          <div class="label">Witness Signature</div>
          <img src="{{ $witnessImagePath }}" style="max-width:100%; max-height:80px; border:1px solid #dddddd; margin-top:4px;" />
        </div>
      @endif

      @if($consent->status === 'approved' && $consent->person)
        @php
          $personSignaturePath = null;
          if ($consent->person->signature_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($consent->person->signature_path)) {
              $personSignaturePath = \Illuminate\Support\Facades\Storage::disk('public')->path($consent->person->signature_path);
          }
        @endphp
        <div style="margin-top:8px;">
          <div class="label">Assigned Person Details</div>
          <div style="font-size:11px; margin-top:2px;">
            <div><strong>Name:</strong> {{ $consent->person->name }}</div>
            @if($consent->person->email)
              <div><strong>Email:</strong> {{ $consent->person->email }}</div>
            @endif
            @if($consent->person->phone)
              <div><strong>Phone:</strong> {{ $consent->person->phone }}</div>
            @endif
          </div>
          @if($personSignaturePath)
            <div class="label">Assigned Person Signature</div>
            <img src="{{ $personSignaturePath }}" style="max-width:100%; max-height:80px; border:1px solid #dddddd; margin-top:4px;" />
          @endif
        </div>
      @endif
    </div>
    @endif

    </div>
    @if($i < $count - 1)
      <div class="page-break"></div>
    @endif
  @endforeach
</body>
</html>
