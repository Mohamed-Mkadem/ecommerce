<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\Client;
use App\Models\Review;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ShippingSetting;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ProductResource;

class AdminController extends Controller
{
    public function dashboard()
    {
        $todaysEarnings = Invoice::getEarnings(true)['day'];
        $todaysInvoicesCount = Invoice::getCount()['day'];
        $todaysOrdersCount = Order::getCount()['day']['total'];
        $todaysReviewsCount = Review::getCount()['total']['day'];
        $todaysClientsCount = Client::getCount()['day'];
        $todaysNotificationsCount = Auth::user()->getTodaysNotificationsCount();

        $orders = Order::latest()->take(4)->get();
        $clients = Client::latest()->take(6)->get();

        $orderCountsByDate = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', \Carbon\Carbon::today()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $weeklyOrders = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            $weeklyOrders->push([
                'label' => $date->format('M j'),
                'count' => $orderCountsByDate[$date->toDateString()] ?? 0,
                'date' => $date->toDateString(),
            ]);
        }

        $weeklyClientOrderCountsByDate = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('source', 'client')
            ->where('created_at', '>=', \Carbon\Carbon::today()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $weeklyClientOrders = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            $weeklyClientOrders->push([
                'label' => $date->format('M j'),
                'count' => $weeklyClientOrderCountsByDate[$date->toDateString()] ?? 0,
                'date' => $date->toDateString(),
            ]);
        }

        $acceptanceDates = ShippingSetting::first();
        return Inertia::render(
            'Admin/Dashboard',
            [
                'todaysEarnings' => $todaysEarnings,
                'todaysInvoicesCount' => $todaysInvoicesCount,
                'todaysOrdersCount' => $todaysOrdersCount,
                'todaysReviewsCount' => $todaysReviewsCount,
                'todaysClientsCount' => $todaysClientsCount,
                'todaysNotificationsCount' => $todaysNotificationsCount,
                'orders' =>   OrderResource::collection($orders),
                'weeklyOrders' => $weeklyOrders,
                'weeklyClientOrders' => $weeklyClientOrders,
                'clients' =>   ClientResource::collection($clients),
                'acceptance_dates' => $acceptanceDates,

            ]
        );
    }
}
