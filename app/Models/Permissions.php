<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permissions extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "permissions";

    protected $fillable = [
        "name",
        "description",
        "granted_by_default",
    ];
}
