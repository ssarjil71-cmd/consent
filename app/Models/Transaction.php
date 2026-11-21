<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'organization_id',
        'consent_id',
        'amount',
        'type',
        'description',
        'balance_after',
        'transaction_id',
        'payment_method',
        'status',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function consent()
    {
        return $this->belongsTo(Consent::class);
    }
}
