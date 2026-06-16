<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponCodeStatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $currentYearStart = Carbon::now()->startOfYear();
        $currentYearEnd = Carbon::now()->endOfYear();
        return [
            'id' => $this->id,
            'value' => $this->value,
            'code' => $this->code,
            'status' => $this->status,

            'count' => [
                'total' => [
                    'orders_count' => $this->orders()->count(),
                    'delivered_orders_count' => $this->orders()->where('status', 'delivered')->count(),
                    'confirmed_orders_count' => $this->orders()->where('status', 'confirmed')->count(),
                    'canceled_orders_count' => $this->orders()->where('status', 'canceled')->count(),
                    'returned_orders_count' => $this->orders()->where('status', 'returned')->count(),
                    'shipped_orders_count' => $this->orders()->where('status', 'shipped')->count(),
                    'pending_orders_count' => $this->orders()->where('status', 'pending')->count(),
                ],
                'day' => [
                    'orders_count' => $this->orders()->whereBetween('orders.created_at', [$todayStart, $todayEnd])->count(),
                    'pending_orders_count' => $this->orders()->where('status', 'pending')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->count(),
                    'confirmed_orders_count' => $this->orders()->where('status', 'confirmed')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->count(),
                    'canceled_orders_count' => $this->orders()->where('status', 'canceled')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->count(),
                    'shipped_orders_count' => $this->orders()->where('status', 'shipped')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->count(),
                    'returned_orders_count' => $this->orders()->where('status', 'returned')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->count(),
                    'delivered_orders_count' => $this->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->count(),

                ],
                'week' => [
                    'orders_count' => $this->orders()->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                    'pending_orders_count' => $this->orders()->where('status', 'pending')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->count(),
                    'confirmed_orders_count' => $this->orders()->where('status', 'confirmed')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->count(),
                    'canceled_orders_count' => $this->orders()->where('status', 'canceled')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->count(),
                    'shipped_orders_count' => $this->orders()->where('status', 'shipped')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->count(),
                    'returned_orders_count' => $this->orders()->where('status', 'returned')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->count(),
                    'delivered_orders_count' => $this->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->count(),

                ],
                'month' => [
                    'orders_count' => $this->orders()->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                    'pending_orders_count' => $this->orders()->where('status', 'pending')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->count(),
                    'confirmed_orders_count' => $this->orders()->where('status', 'confirmed')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->count(),
                    'canceled_orders_count' => $this->orders()->where('status', 'canceled')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->count(),
                    'shipped_orders_count' => $this->orders()->where('status', 'shipped')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->count(),
                    'returned_orders_count' => $this->orders()->where('status', 'returned')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->count(),
                    'delivered_orders_count' => $this->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->count(),

                ],
                'year' => [
                    'orders_count' => $this->orders()->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])->count(),
                    'pending_orders_count' => $this->orders()->where('status', 'pending')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->count(),
                    'confirmed_orders_count' => $this->orders()->where('status', 'confirmed')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->count(),
                    'canceled_orders_count' => $this->orders()->where('status', 'canceled')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->count(),
                    'shipped_orders_count' => $this->orders()->where('status', 'shipped')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->count(),
                    'returned_orders_count' => $this->orders()->where('status', 'returned')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->count(),
                    'delivered_orders_count' => $this->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->count(),

                ],

            ]
        ];
    }
}
