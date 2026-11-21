<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'organization_id',
        'organization_type_id',
        'created_by_admin',
    ];

    /**
     * Get the organization that owns the category.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
    
    /**
     * The organizations that belong to the category.
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class);
    }

    /**
     * Get the organization type that owns the category.
     */
    public function organizationType(): BelongsTo
    {
        return $this->belongsTo(OrganizationType::class);
    }

    /**
     * Get the admin who created this category.
     */
    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin');
    }

    /**
     * Scope a query to only include categories for a specific organization type.
     */
    public function scopeForOrganizationType(Builder $query, $organizationTypeId): Builder
    {
        return $query->where('organization_type_id', $organizationTypeId);
    }

    /**
     * Scope a query to only include global categories (created by admin for all organizations of a type).
     */
    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('organization_id');
    }

    /**
     * Scope a query to only include organization-specific categories.
     */
    public function scopeForOrganization(Builder $query, $organizationId): Builder
    {
        return $query->where('organization_id', $organizationId);
    }
}
