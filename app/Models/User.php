<?php

namespace App\Models;

use App\Enums\UserRoles as DefaultRoles;
use App\Models\UserRoles as Roles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';

    protected $table = "users";

    protected $fillable = [
        "name",
        "lastname",
        "username",
        "email",
        "password",
        "blocked",
        "remember_token"
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed'
    ];

    public function news(): HasMany
    {
        return $this->hasMany(UserNews::class);
    }

    public function newsImages(): HasMany
    {
        return $this->hasMany(UserNewsImages::class);
    }

    public function roles(): HasOne
    {
        return $this->hasOne(Roles::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(UserMessages::class);
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(UserWarnings::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotifications::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(UserReactions::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(UserPermissions::class);
    }

    /**
     * Check if the user role is administrator
     *
     * @return boolean
     */
    public function isAdministrator() {
        return $this->role_id === DefaultRoles::Admin->value;
    }

    /**
     * Check if the user role is moderator
     *
     * @return boolean
     */
    public function isModerator() {
        return $this->role_id === DefaultRoles::Moderator->value;
    }

    /**
     * Check if the user role is writer
     *
     * @return boolean
     */
    public function isWriter() {
        return $this->role_id === DefaultRoles::Writer->value;
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
