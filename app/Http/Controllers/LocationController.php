<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function cities($stateId)
    {
        return City::where('state_id', $stateId)->get();
    }

    public function localities($cityId)
    {
        return City::findOrFail($cityId)->localities()->get();
    }
}
