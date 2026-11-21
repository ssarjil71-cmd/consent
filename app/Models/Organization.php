<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomResetPassword;

class Organization extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'type_id',
        'name',
        'email',
        'mobile',
        'password',
        'status',
        'transaction_amount',
        'per_consent_amount',
        'consent_label',
        'terms_text',
        'terms_text_hi',
        'terms_text_mr',
        'liability_release_text',
        'liability_release_text_hi',
        'liability_release_text_mr',
        'risk_acknowledgement_text',
        'risk_acknowledgement_text_hi',
        'risk_acknowledgement_text_mr',
        'terms_accepted_at',
        'liability_accepted_at',
        'activation_fee',
        'paid_amount',
        'payment_status',
        'payment_id',
        'payment_date',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'payment_date' => 'datetime',
        'terms_accepted_at' => 'datetime',
        'liability_accepted_at' => 'datetime',
    ];

    /**
     * The guard for the model.
     *
     * @var string
     */
    protected $guard = 'organization';

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function type()
    {
        return $this->belongsTo(OrganizationType::class, 'type_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function organization_type()
    {
        return $this->belongsTo(OrganizationType::class, 'type_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
