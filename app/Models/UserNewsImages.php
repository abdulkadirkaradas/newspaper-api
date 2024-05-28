<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This model is the pivot.
 * It's used solely for establishing relations between the User, News, and NewsImages models.
 */
class UserNewsImages extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "user_news";

    protected $fillable = [
        "user_id",
        "news_id",
        "news_img_id",
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
