<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserNews extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

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
