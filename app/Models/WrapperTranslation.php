<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WrapperTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
    ];

    public function wrapper()
    {
        return $this->belongsTo(Wrapper::class);
    }
}
