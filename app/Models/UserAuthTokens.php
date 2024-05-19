<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class UserAuthTokens extends Model
{
    use HasFactory, HasUuids, HasApiTokens;

    protected static function boot() {
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
        "last_login"
    ];

    protected $dates = [
        "expire_date",
        "last_login"
    ];

    private function setExpireDate() {
        $this->attributes['expire_date'] = now()->addDays(15);
    }

    private function setLastLogin() {
        $this->attributes['last_login'] = now();
    }
}
