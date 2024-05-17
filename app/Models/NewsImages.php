<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsImages extends Model
{
    use HasFactory, HasUuids;

    protected $table = "news_images";

    protected $fillable = [
        "name",
        "ext",
        "fullpath",
        "user_id",
        "news_id"
    ];
}
