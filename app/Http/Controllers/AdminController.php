<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Http\Resources\OrderResource;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Nrp;
use App\Models\Order;
use App\Models\ShippingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $todaysEarnings = Invoice::getEarnings(true)['day'];
        $todaysInvoicesCount = Invoice::getCount()['day'];
        $todaysOrdersCount = Order::getCount()['day']['total'];
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
                'todaysClientsCount' => $todaysClientsCount,
                'todaysNotificationsCount' => $todaysNotificationsCount,
                'orders' =>   OrderResource::collection($orders),
                'weeklyOrders' => $weeklyOrders,
                'weeklyClientOrders' => $weeklyClientOrders,
                'clients' =>   ClientResource::collection($clients),
                'acceptance_dates' => $acceptanceDates,
                'defaultRatesStart' => now()->startOfMonth()->format('Y-m-d'),
                'defaultRatesEnd'   => now()->endOfMonth()->format('Y-m-d'),
                'defaultDeliveryStart' => now()->startOfMonth()->format('Y-m-d'),
                'defaultDeliveryEnd'   => now()->endOfMonth()->format('Y-m-d'),

            ]
        );
    }

     public function chartsData(Request $request)
    {
        $ratesStartDate = $request->input('rates_start_date', now()->startOfMonth()->format('Y-m-d'));
        $ratesEndDate = $request->input('rates_end_date', now()->endOfMonth()->format('Y-m-d'));
        $deliveryStartDate = $request->input('delivery_start_date', now()->startOfMonth()->format('Y-m-d'));
        $deliveryEndDate = $request->input('delivery_end_date', now()->endOfMonth()->format('Y-m-d'));

        // --- Order Rates (Confirmed / Canceled / NRP) ---

        // 1. Confirmed: Orders with status in [confirmed, shipped, delivered, returned]
        $confirmedCount = Order::whereIn('status', ['confirmed', 'shipped', 'delivered', 'returned'])
            ->whereBetween('created_at', [
                $ratesStartDate . ' 00:00:00',
                $ratesEndDate . ' 23:59:59'
            ])
            ->count();

        // 2. Canceled: Orders with status = canceled
        $canceledCount = Order::where('status', 'canceled')
            ->whereBetween('created_at', [
                $ratesStartDate . ' 00:00:00',
                $ratesEndDate . ' 23:59:59'
            ])
            ->count();

        // 3. NRP: Count distinct orders that have at least one NRP record in the date range
        //    Each order counted only once, regardless of how many NRP attempts were made.
        $nrpCount = Nrp::whereBetween('created_at', [
            $ratesStartDate . ' 00:00:00',
            $ratesEndDate . ' 23:59:59'
        ])
            ->distinct('order_id')
            ->count('order_id');

        // Build the orderRates array
        $orderRates = [
            'order.confirmed' => $confirmedCount,
            'order.canceled'  => $canceledCount,
            'order.nrp'       => $nrpCount,
        ];

        // --- Delivery / Return Rates (unchanged) ---

        $deliveryRates = Order::whereIn('status', ['delivered', 'returned'])
            ->when($deliveryStartDate, fn($q) => $q->whereDate('delivery_date', '>=', $deliveryStartDate))
            ->when($deliveryEndDate, fn($q) => $q->whereDate('delivery_date', '<=', $deliveryEndDate))
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->mapWithKeys(fn($count, $status) => ["order.{$status}" => $count]);

        return response()->json([
            'orderRates' => $orderRates,
            'deliveryRates' => $deliveryRates,
            'ratesFilters' => [
                'start_date' => $ratesStartDate,
                'end_date' => $ratesEndDate,
            ],
            'deliveryFilters' => [
                'start_date' => $deliveryStartDate,
                'end_date' => $deliveryEndDate,
            ],
        ]);
    }
}
