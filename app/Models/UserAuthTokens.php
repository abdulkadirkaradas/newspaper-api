<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class UserAuthTokens extends Model
{
    use HasFactory, HasUuids, HasApiTokens;

    protected $table = "user_auth_tokens";

    protected $fillable = [
        "token",
        "user_id",
        "expire_date",
        "last_login"
    ];
}
