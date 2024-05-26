<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPermissions extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "user_permissions";

    protected $fillable = [
        "granted",
        "permission_id",
        "user_id"
    ];

    public function user() {
        return $this->belongsToMany(User::class);
    }
}
