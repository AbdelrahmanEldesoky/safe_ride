<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Spatie\Translatable\HasTranslations;

class Region extends Model
{
    use HasFactory, SpatialTrait , HasTranslations;
    public $translatable = ['name'];
//    protected $guarded = [];
    protected $fillable = ['distance_unit', 'status', 'timezone' ];

    protected $spatialFields = [
        'coordinates'
    ];

    protected $casts = [
        'status' => 'integer',
    ];



}
