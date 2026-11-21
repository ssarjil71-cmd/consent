<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'phone',
        'occupation',
        'signature_path',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
