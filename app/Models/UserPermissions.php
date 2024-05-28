<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPermissions extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "user_permissions";

    protected $fillable = [
        "user_id",
        "user_role_id",
        "permission_id",
    ];

    public function userRoles(): BelongsTo
    {
        return $this->belongsTo(UserRoles::class);
    }
}
