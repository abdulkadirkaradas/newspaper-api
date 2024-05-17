<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisements extends Model
{
    use HasFactory, HasUuids;

    protected $table = "advertisements";

    protected $fillable = [
        "title",
        "content",
    ];
}
