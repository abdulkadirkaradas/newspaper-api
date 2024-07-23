<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "roles";

    protected $fillable = [
        "name",
        "description",
        "type"
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
