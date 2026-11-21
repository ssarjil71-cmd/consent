<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationType extends Model
{
    protected $fillable = [
        'name', 
        'description',
        'terms_text',
        'liability_release_text',
        'risk_acknowledgement_text'
    ];

    public function organizations()
    {
        return $this->hasMany(Organization::class, 'type_id');
    }
}
