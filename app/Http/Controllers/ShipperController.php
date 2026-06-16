<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShipperResource;
use App\Models\Shipper;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShipperController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $shippers = Shipper::paginate();
        return Inertia::render('Admin/Shippers/Index', [
            'shippers' => ShipperResource::collection($shippers)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Shippers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'unique:shippers,name', 'string']
        ]);

        Shipper::create(['name' => $request->name]);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipper $shipper)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipper $shipper)
    {
        return Inertia::render('Admin/Shippers/Edit', ['shipper' => $shipper]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shipper $shipper)
    {
        $request->validate([
            'name' => ['required', 'unique:shippers,name', 'string']
        ]);

        $shipper->name = $request->name;
        $shipper->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipper $shipper)
    {

        if ($shipper->orders->count() > 0) {
            $shipper->delete();
        } else {
            $shipper->forceDelete();
        }


        return redirect()->back();
    }
}
