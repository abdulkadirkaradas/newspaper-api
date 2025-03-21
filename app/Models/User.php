<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'id';

    protected $table = "users";

    protected $fillable = [
        "name",
        "lastname",
        "username",
        "email",
        "password",
        "blocked",
        "remember_token",
        "role_id"
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed'
    ];

    public function authTokens(): HasOne
    {
        return $this->hasOne(UserAuthTokens::class);
    }

    public function roles(): HasOne
    {
        return $this->hasOne(Role::class);
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(Warning::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')->withTimestamps();
    }

    // public function hasPermission(string $permission_id) {
    //     return $this->roles()->whereHas('permissions', function ($query) use($permission_id) {
    //         $query->where('id', $permission_id);
    //     })->exists();
    // }

    /**
     * Check if user has proper role
     *
     * @param int $role_id
     * @return bool
     */
    public function hasRole(int $role_id)
    {
        return $this->role_id === $role_id;
    }

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
