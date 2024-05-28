<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This model is the pivot.
 * It's used solely for establishing relations between the User, Roles and Permissions models.
 */
class UserPermissions extends Pivot
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "user_permissions";

    protected $fillable = [
        "user_id",
        "user_role_id",
        "permission_id",
    ];
}
