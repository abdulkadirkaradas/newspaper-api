<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OppositeNews extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "opposite_news";

    protected $fillable = [
        "source_user_id",
        "opposite_user_id",
        "source_news_id",
        "opposite_news_id",
    ];
}
