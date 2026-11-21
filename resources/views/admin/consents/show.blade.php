@extends('admin.layout.master')

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

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3>Consent Details</h3>
        <small class="text-muted">{{ $consent->organization->name ?? 'Unknown Organization' }}</small>
    </div>
    <a href="{{ route('admin.consents.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Consents
    </a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Basic Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="mb-3">Basic Information</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Consent ID:</strong></td>
                        <td>{{ $consent->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $consent->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $consent->email ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td>{{ $consent->phone ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Age:</strong></td>
                        <td>{{ $consent->age ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Sex:</strong></td>
                        <td>{{ $sexLabel }}</td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td>{{ $consent->address ?: '—' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="mb-3">Consent Information</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge bg-{{ $consent->status === 'approved' ? 'success' : ($consent->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($consent->status ?? 'pending') }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Consent For:</strong></td>
                        <td>{{ ucfirst($consent->consent_for ?: 'self') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Relative Name:</strong></td>
                        <td>{{ $relativeName }}</td>
                    </tr>
                    <tr>
                        <td><strong>Relative Relation:</strong></td>
                        <td>{{ $relativeRelation }}</td>
                    </tr>
                    <tr>
                        <td><strong>Registration Number:</strong></td>
                        <td>{{ $consent->registration_number ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Language:</strong></td>
                        <td>{{ strtoupper($consent->lang ?? 'en') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td>{{ $consent->created_at?->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Notes Section -->
        @if($consent->notes || $consent->notes_hi || $consent->notes_mr)
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="mb-3">Notes</h5>
                <div class="card">
                    <div class="card-body">
                        @if($consent->notes)
                        <div class="mb-2">
                            <strong>English:</strong><br>
                            {{ $consent->notes }}
                        </div>
                        @endif
                        @if($consent->notes_hi)
                        <div class="mb-2">
                            <strong>Hindi:</strong><br>
                            {{ $consent->notes_hi }}
                        </div>
                        @endif
                        @if($consent->notes_mr)
                        <div class="mb-2">
                            <strong>Marathi:</strong><br>
                            {{ $consent->notes_mr }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Verification Details -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="mb-3">Verification Details</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Mobile Verified:</strong></td>
                        <td>
                            @if($consent->otp_verified_at)
                                <span class="badge bg-success">Yes</span>
                                <small class="text-muted">({{ $consent->otp_verified_at->format('d M Y, h:i A') }})</small>
                            @else
                                <span class="badge bg-danger">No</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>KYC Type:</strong></td>
                        <td>{{ $consent->kyc_type ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>KYC Number:</strong></td>
                        <td>{{ $consent->kyc_number ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>KYC Verified:</strong></td>
                        <td>
                            @if($consent->kyc_verified_at)
                                <span class="badge bg-success">Yes</span>
                                <small class="text-muted">({{ $consent->kyc_verified_at->format('d M Y, h:i A') }})</small>
                            @else
                                <span class="badge bg-danger">No</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="mb-3">Location Information</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Client IP:</strong></td>
                        <td>{{ $consent->client_ip ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Client Location:</strong></td>
                        <td>{{ $consent->client_location ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Latitude:</strong></td>
                        <td>{{ $consent->client_latitude ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Longitude:</strong></td>
                        <td>{{ $consent->client_longitude ?: '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Signature Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="mb-3">Signature Information</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Signature Method:</strong></td>
                        <td>{{ ucfirst($consent->signature_method ?: '—') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Signature Note:</strong></td>
                        <td>{{ $consent->signature_note ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Signature Upload:</strong></td>
                        <td>
                            @if($consent->signature_upload_path)
                                <a href="{{ asset('storage/' . $consent->signature_upload_path) }}" target="_blank" class="btn btn-sm btn-primary">View Signature</a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="mb-3">Witness Information</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Witness Name:</strong></td>
                        <td>{{ $consent->witness_name ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Witness Phone:</strong></td>
                        <td>{{ $consent->witness_phone ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Witness Email:</strong></td>
                        <td>{{ $consent->witness_email ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Witness Relation:</strong></td>
                        <td>{{ $consent->witness_relation ?: '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- File Uploads -->
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3">File Uploads</h5>
                <div class="row">
                    @if($consent->thumb_upload_path)
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">Thumb Impression</h6>
                                <a href="{{ asset('storage/' . $consent->thumb_upload_path) }}" target="_blank" class="btn btn-sm btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($consent->selfie_upload_path)
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">Selfie</h6>
                                <a href="{{ asset('storage/' . $consent->selfie_upload_path) }}" target="_blank" class="btn btn-sm btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($consent->id_proof_upload_path)
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">ID Proof</h6>
                                <a href="{{ asset('storage/' . $consent->id_proof_upload_path) }}" target="_blank" class="btn btn-sm btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($consent->thumb_video_path)
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">Thumb Video</h6>
                                <a href="{{ asset('storage/' . $consent->thumb_video_path) }}" target="_blank" class="btn btn-sm btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($consent->witness_signature_path)
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">Witness Signature</h6>
                                <a href="{{ asset('storage/' . $consent->witness_signature_path) }}" target="_blank" class="btn btn-sm btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
