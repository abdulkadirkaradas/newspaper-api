<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRoles extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "user_roles";

    protected $fillable = [
        "user_id",
        "role_id",
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
