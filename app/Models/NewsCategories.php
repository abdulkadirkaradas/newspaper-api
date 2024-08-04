<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsCategories extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "news_categories";

    protected $fillable = [
        "name",
        "description",
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}
