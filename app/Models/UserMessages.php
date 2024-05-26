<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMessages extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "user_messages";

    protected $fillable = [
        "warning_text",
        "user_id"
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
