<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BadgeImage extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "badge_images";

    protected $fillable = [
        "name",
        "ext",
        "fullpath",
        "user_id",
        "badge_id",
    ];

    public function badges(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }
}
