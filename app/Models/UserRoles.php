<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * This model is the pivot.
 * It's used solely for establishing relations between the User and Roles models.
 */
class UserRoles extends Pivot
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "user_roles";

    protected $fillable = [
        "user_id",
        "role_id",
    ];
}
