<?php

namespace App\Http\Controllers;

use App\Http\Resources\StateResource;
use App\Models\Shipper;
use App\Models\ShippingSetting;
use App\Models\State;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $states = State::all();
        $acceptanceDates = ShippingSetting::first();

        return Inertia::render('Admin/States/Index', [
            'states' =>
            StateResource::collection($states),
            'acceptance_dates' => $acceptanceDates,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(State $state)
    {
        $shippers = Shipper::all();

        return Inertia::render('Admin/States/Edit', [
            'state' => $state,
            'shippers' => $shippers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'delivery_cost' => ['required', 'numeric', 'min:0'],
            'return_cost' => ['required', 'numeric', 'min:0'],
            'default_shipper_id' => ['nullable', 'exists:shippers,id'],
        ]);

        $state->update([
            'shipping_cost' => $request->price * 1000,
            'delivery_cost' => $request->delivery_cost * 1000,
            'return_cost' => $request->return_cost * 1000,
            'default_shipper_id' => $request->default_shipper_id,
        ]);
        $state->save();

        return redirect()->route('states.index');
    }
}
