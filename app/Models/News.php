<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "news";

    protected $fillable = [
        "title",
        "content",
        "user_id"
    ];

    public function newsImages() {
        return $this->hasMany(NewsImages::class);
    }

    public function newsReactions() {
        return $this->hasMany(NewsReactions::class);
    }
}
