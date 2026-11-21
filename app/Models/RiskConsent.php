<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RiskConsent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'category_id',
        'title',
        'content_en',
        'content_hi',
        'content_mr',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the organization that owns the risk consent.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the category attached to the risk consent.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Retrieve the languages that have content stored.
     */
    public function getActiveLanguagesAttribute(): Collection
    {
        return collect([
            'en' => filled($this->content_en),
            'hi' => filled($this->content_hi),
            'mr' => filled($this->content_mr),
        ])->filter()->keys();
    }
}
