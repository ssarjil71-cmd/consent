<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'amount',
        'duration_months',
        'features',
        'is_active',
        'max_consents',
        'per_consent_amount',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'amount' => 'decimal:2',
        'per_consent_amount' => 'decimal:2',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }
}
