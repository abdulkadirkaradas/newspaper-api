<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsImages extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "news_images";

    protected $fillable = [
        "name",
        "ext",
        "fullpath",
        "user_id",
        "news_id",
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}
