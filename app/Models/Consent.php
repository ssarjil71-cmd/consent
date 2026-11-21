<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consent extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'phone',
        'notes',
        'notes_hi',
        'notes_mr',
        'status',
        'lang',
        'consent_for',
        'relative_name',
        'relative_relation',
        'age',
        'sex',
        'address',
        'registration_number',
        'person_id',
        'client_ip',
        'client_location',
        'client_latitude',
        'client_longitude',
        'signature_method',
        'signature_note',
        'signature_upload_path',
        'thumb_upload_path',
        'selfie_upload_path',
        'id_proof_upload_path',
        'thumb_video_path',
        'witness_signature_path',
        'witness_name',
        'witness_phone',
        'witness_email',
        'witness_relation',
        'risk_consent_text',
        'risk_consent_text_en',
        'risk_consent_text_hi',
        'risk_consent_text_mr',
        'risk_acknowledgement_text',
        'risk_acknowledgement_text_en',
        'risk_acknowledgement_text_hi',
        'risk_acknowledgement_text_mr',
        'risk_acknowledged',
        'terms_acknowledged',
        'terms_text',
        'terms_text_en',
        'terms_text_hi',
        'terms_text_mr',
        'liability_release_text',
        'liability_release_text_en',
        'liability_release_text_hi',
        'liability_release_text_mr',
        'liability_release_acknowledged',
        'otp_code',
        'otp_verified_at',
        'kyc_type',
        'kyc_number',
        'kyc_verification_method',
        'kyc_verified_at',
    ];

    protected $casts = [
        'notes' => 'string',
        'notes_hi' => 'string',
        'notes_mr' => 'string',
        'relative_name' => 'string',
        'relative_relation' => 'string',
        'age' => 'integer',
        'sex' => 'string',
        'address' => 'string',
        'registration_number' => 'string',
        'client_ip' => 'string',
        'client_location' => 'string',
        'client_latitude' => 'float',
        'client_longitude' => 'float',
        'signature_method' => 'string',
        'signature_note' => 'string',
        'signature_upload_path' => 'string',
        'thumb_upload_path' => 'string',
        'selfie_upload_path' => 'string',
        'id_proof_upload_path' => 'string',
        'thumb_video_path' => 'string',
        'witness_signature_path' => 'string',
        'risk_consent_text' => 'string',
        'risk_consent_text_en' => 'string',
        'risk_consent_text_hi' => 'string',
        'risk_consent_text_mr' => 'string',
        'risk_acknowledgement_text' => 'string',
        'risk_acknowledgement_text_en' => 'string',
        'risk_acknowledgement_text_hi' => 'string',
        'risk_acknowledgement_text_mr' => 'string',
        'risk_acknowledged' => 'boolean',
        'terms_acknowledged' => 'boolean',
        'terms_text' => 'string',
        'terms_text_en' => 'string',
        'terms_text_hi' => 'string',
        'terms_text_mr' => 'string',
        'liability_release_text' => 'string',
        'liability_release_text_en' => 'string',
        'liability_release_text_hi' => 'string',
        'liability_release_text_mr' => 'string',
        'liability_release_acknowledged' => 'boolean',
        'otp_code' => 'string',
        'otp_verified_at' => 'datetime',
        'kyc_type' => 'string',
        'kyc_number' => 'string',
        'kyc_verification_method' => 'string',
        'kyc_verified_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
