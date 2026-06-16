<?php

namespace App\Http\Controllers;

use App\Jobs\SendFacebookConversionEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FacebookTrackingController extends Controller
{
    // This controller previously handled AddToCart and InitiateCheckout via CAPI.
    // We now track those events exclusively with the Meta Pixel on the frontend,
    // and only send Purchase via the Conversions API from OrderController.
}
