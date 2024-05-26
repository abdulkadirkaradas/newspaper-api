<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReactions extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "user_reactions";

    protected $fillable = [
        "reaction",
        "user_id",
        "news_id",
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
