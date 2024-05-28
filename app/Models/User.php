<?php

namespace App\Models;

use App\Enums\UserRoles as DefaultRoles;
use App\Models\UserRoles as Roles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

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
        "remember_token"
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed'
    ];

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'user_news')
            ->withPivot('user_id', 'news_id', 'reaction_id');
    }

    public function newsImages(): BelongsToMany
    {
        return $this->belongsToMany(NewsImages::class, 'user_news_images')
            ->withPivot('user_id', 'news_id', 'news_img_id');
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

    public function hasPermission(string $permission_id) {
        $this->roles->permissions()->where('permission_id', $permission_id)->exists();
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
