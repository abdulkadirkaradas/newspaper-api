<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "news";

    protected $fillable = [
        "title",
        "content",
        "priority",
        "approved",
        "visibility",
        "opposition",
        "approved_by",
        "removed_by",
        "user_id",
        "category_id",
    ];

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function newsImages(): HasMany
    {
        return $this->hasMany(NewsImages::class);
    }
    public function newsReactions(): HasMany
    {
        return $this->hasMany(NewsReactions::class);
    }
}
