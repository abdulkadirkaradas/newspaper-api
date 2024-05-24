<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsReactions extends Model
{
    use HasFactory, HasUuids;

    protected $table = "news_reactions";

    protected $fillable = [
        "reaction",
        "user_id",
        "news_id"
    ];

    public function news() {
        return $this->belongsTo(News::class);
    }
}
