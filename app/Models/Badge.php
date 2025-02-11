<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "badges";

    protected $fillable = [
        "name",
        "description",
        "type",
        "user_id",
    ];

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')->withTimestamps();
    }
    public function badgeImages(): HasMany
    {
        return $this->hasMany(BadgeImage::class);
    }
}
