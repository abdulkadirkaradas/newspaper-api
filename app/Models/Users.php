<?php

namespace App\Models;

use App\Enums\UserRoles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Users extends Authenticatable implements JWTSubject
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

    /**
     * Check if the user role is administrator
     *
     * @return boolean
     */
    public function isAdministrator() {
        return $this->role_id === UserRoles::Admin->value;
    }

    /**
     * Check if the user role is moderator
     *
     * @return boolean
     */
    public function isModerator() {
        return $this->role_id === UserRoles::Moderator->value;
    }

    /**
     * Check if the user role is writer
     *
     * @return boolean
     */
    public function isWriter() {
        return $this->role_id === UserRoles::Writer->value;
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
