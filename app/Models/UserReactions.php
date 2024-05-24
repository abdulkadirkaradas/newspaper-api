<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReactions extends Model
{
    use HasFactory, HasUuids;

    protected $table = "user_reactions";

    protected $fillable = [
        "user_id",
        "news_id",
        "reaction",
    ];
}
