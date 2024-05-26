<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserNotifications extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "user_notifications";

    protected $fillable = [
        "notification",
        "user_id",
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
