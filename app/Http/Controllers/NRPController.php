<?php

namespace App\Http\Controllers;

use App\Http\Resources\NRPResource;
use App\Models\Nrp;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Contracts\Activity;

class NRPController extends Controller
{
    public function index(Request $request)
    {

        $query = Nrp::query();

        if($request->filled('phone')){
            $query->whereHas('order', function ($q) use ($request){
                $q->where('phone', 'like', '%' . $request->input('phone') . '%');
            });
        }

        if($request->filled('minAmount')){
            $query->whereHas('order', function ($q) use ($request){
                $q->where('amount', '>=', ($request->input('minAmount') * 1000));
            });
        }
        if($request->filled('maxAmount')){
            $query->whereHas('order', function ($q) use ($request){
                $q->where('amount', '<=', ($request->input('maxAmount') * 1000));
            });
        }


        if($request->filled('minTries')){
            $query->where('tries', '>=', $request->input('minTries'));
        }
        if($request->filled('maxTries')){
            $query->where('tries', '<=', $request->input('maxTries'));
        }

        if ($request->filled('minCreationDate')) {
            $query->whereDate('created_at', '>=', $request->minCreationDate);
        }

        if ($request->filled('maxCreationDate')) {
            $maxDateTime = \Carbon\Carbon::parse($request->maxCreationDate)->endOfDay();
            $query->where('created_at', '<=', $maxDateTime);
        }


        switch ($request->input('sort')) {
            case 'highest_amount':
            $query->join('orders', 'nrps.order_id', '=', 'orders.id')
                  ->orderBy('orders.amount', 'desc')
                  ->select('nrps.*');
            break;
            case 'lowest_amount':
            $query->join('orders', 'nrps.order_id', '=', 'orders.id')
                  ->orderBy('orders.amount', 'asc')
                  ->select('nrps.*');
            break;
            case 'highest_tries':
            $query->orderBy('tries', 'desc');
            break;
            case 'lowest_tries':
            $query->orderBy('tries', 'asc');
            break;
            case 'oldest':
            $query->orderBy('created_at', 'asc');
            break;
            default:
            $query->orderBy('created_at', 'desc');
        }

         if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('phone', 'like', '%' . $search . '%')
                  ->orWhere('amount', 'like', '%' . $search . '%');
            });
        }

   
        $nrps = $query->paginate(10)->withQueryString();

         return Inertia::render('Admin/NRPs/Index',['nrps' => NRPResource::collection($nrps), 'filters' => $request->all()]);
    }

    public function store(Order $order)
    {

       
    if ($order->nrp) {
        $order->nrp->increment('tries');
    } else {
      
        $order->nrp()->create(['tries' => 1]);
    }

    activity()
        ->by(auth()->user())
        ->on($order)
        ->tap(function ($activiy){
            $activiy->icon = 'ri-phone-fill';
        })
        ->log('order.nrp');

    return redirect()->back()->with('success', 'Order marked as nrp successfully');

    }
}
