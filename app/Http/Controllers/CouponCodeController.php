<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CouponCodeController extends Controller
{

    protected $messages = [
        'ar' => [
            'value.between' => 'قيمة التخفيض يجب أن تكون بين 1 و100'
        ],
        'fr' => [
            'value.between' => "La remise doit être comprise entre 1 et 100."
        ],
        'en' => [
            'value.between' => "The discount must be between 1 and 100."
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $couponCodes = CouponCode::withCount('orders')->paginate(20);

        return Inertia::render('Admin/Coupon Codes/Index', ['codes' => $couponCodes]);
    }

    public function getCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string']
        ]);
        $couponCode = CouponCode::where('code', $request->code)->where('status', 'active')->first();

        return $couponCode;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Coupon Codes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'status' => ['required', 'in:active,inactive'],
            'code' => ['required', 'string', 'unique:coupon_codes,code'],
            'value' => ['required', 'numeric', 'between:1,100'],
        ], $this->messages[app()->getLocale()]);


        CouponCode::create([
            'status' => $request->status,
            'code' => $request->code,
            'value' => $request->value,
        ]);

        return redirect()->route('coupons.index');
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CouponCode $coupon)
    {
        return Inertia::render('Admin/Coupon Codes/Edit', ['code' => $coupon]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CouponCode $coupon)
    {
        $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $coupon->status = $request->status;
        $coupon->save();

        return redirect()->route('coupons.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CouponCode $coupon)
    {

        if ($coupon->orders()->exists()) {
            $coupon->delete();
        } else {
            $coupon->forceDelete();
        }

        return redirect()->route('coupons.index');
    }
}
