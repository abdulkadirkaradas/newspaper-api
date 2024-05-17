<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermissions extends Model
{
    use HasFactory, HasUuids;

    protected $table = "user_permissions";

    protected $fillable = [
        "granted",
        "permission_id",
        "user_id"
    ];
}
