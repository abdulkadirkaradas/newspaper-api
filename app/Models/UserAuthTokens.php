<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAuthTokens extends Model
{
    use HasFactory, HasUuids, HasApiTokens, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->setExpireDate();
            $model->setLastLogin();
        });
    }

    protected $table = "user_auth_tokens";

    protected $fillable = [
        "token",
        "user_id",
        "expire_date",
        "last_login",
        "expired"
    ];

    protected $dates = [
        "expire_date",
        "last_login"
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    private function setExpireDate()
    {
        $this->attributes['expire_date'] = now()->addDays(15);
    }

    private function setLastLogin()
    {
        $this->attributes['last_login'] = now();
    }
}