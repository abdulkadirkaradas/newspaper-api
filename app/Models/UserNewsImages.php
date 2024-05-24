<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNewsImages extends Model
{
    use HasFactory, HasUuids;

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
