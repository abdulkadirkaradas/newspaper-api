<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNews extends Model
{
    use HasFactory, HasUuids;

    protected $table = "user_news";

    protected $fillable = [
        "news_id",
        "user_id",
        "reaction_id",
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
