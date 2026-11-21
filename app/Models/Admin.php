<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use App\Notifications\CustomResetPassword;
use App\Models\Permission;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admins';

    /**
     * The guard for the model.
     *
     * @var string
     */
    protected $guard = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Cached list of resolved permission slugs for the admin user.
     */
    protected $cachedPermissionSlugs;

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'admin_permission')->withTimestamps();
    }

    /**
     * Resolve all permission slugs assigned directly or via role.
     */
    public function permissionSlugs(): array
    {
        if ($this->cachedPermissionSlugs !== null) {
            return $this->cachedPermissionSlugs;
        }

        $this->loadMissing(['permissions:id,slug', 'role.permissions:id,slug']);

        $slugs = $this->permissions->pluck('slug')->toArray();

        if ($this->role) {
            $slugs = array_merge($slugs, $this->role->permissions->pluck('slug')->toArray());
        }

        $normalized = collect($slugs)
            ->filter()
            ->map(fn ($slug) => strtolower($slug))
            ->unique()
            ->values()
            ->all();

        return $this->cachedPermissionSlugs = $normalized;
    }

    /**
     * Determine if the admin has access to the given permission slug.
     */
    public function hasPermission(string|array|null $slug): bool
    {
        if ($slug === null || $slug === '') {
            return true;
        }

        if ($this->role_id === null) {
            return true;
        }

        $resolved = $this->permissionSlugs();

        $slugs = is_array($slug) ? $slug : [$slug];

        foreach ($slugs as $candidate) {
            if (in_array(strtolower($candidate), $resolved, true)) {
                return true;
            }
        }

        return false;
    }

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

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
