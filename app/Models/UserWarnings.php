<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWarnings extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "user_warnings";

    protected $fillable = [
        "message",
        "warning_level",
        "user_id",
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
