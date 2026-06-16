<?php

namespace App\Models;

use App\Models\State;
use App\Models\Locality;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class City extends Model  implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['name'];
    protected $fillable = ['state_id'];
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function localities()
    {
        return $this->hasMany(Locality::class);
    }
}
