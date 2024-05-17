<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
    use HasFactory;

    protected $table = "sessions";

    protected $fillable = [
        "user_id",
        "ip_address",
        "user_agent",
        "last_activity"
    ];
}
