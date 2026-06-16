<?php

namespace App\Models;

use App\Models\City;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class Locality extends Model
{

    use Translatable;

    protected $fillable = ['city_id'];
    public $translatedAttributes = ['name'];
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
