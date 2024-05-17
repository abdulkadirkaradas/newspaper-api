<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertisementImages extends Model
{
    use HasFactory, HasUuids;

    protected $table = "advertisement_images";

    protected $fillable = [
        "name",
        "ext",
        "fullpath",
        "ads_id"
    ];
}
