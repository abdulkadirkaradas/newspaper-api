<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMessages extends Model
{
    use HasFactory, HasUuids;

    protected $table = "user_messages";

    protected $fillable = [
        "warning_text",
        "user_id"
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
