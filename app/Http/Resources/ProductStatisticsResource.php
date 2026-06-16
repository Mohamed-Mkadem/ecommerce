<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductStatisticsResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->getFormattedPrice(),
            'status' => $this->status,
            'type' => $this->type,
            'rate' => $this->rate && $this->rate != 0 ? $this->rate : null,
            'main_image_url' => $this->getFirstMediaUrl('images') ?: asset('storage/products/default.png'),
            'ends_at' => $this->getFormattedEndsAtDate(),
            'media' => $this->getMedia('images')->toArray(),
            'counts' => [
                'total' => [
                    'orders_count' => $this->orders()->sum('order_product.quantity'),
                    'delivered_orders_count' => $this->orders()->where('status', 'delivered')->sum('order_product.quantity'),
                    'confirmed_orders_count' => $this->orders()->where('status', 'confirmed')->sum('order_product.quantity'),
                    'canceled_orders_count' => $this->orders()->where('status', 'canceled')->sum('order_product.quantity'),
                    'returned_orders_count' => $this->orders()->where('status', 'returned')->sum('order_product.quantity'),
                    'shipped_orders_count' => $this->orders()->where('status', 'shipped')->sum('order_product.quantity'),
                    'pending_orders_count' => $this->orders()->where('status', 'pending')->sum('order_product.quantity'),
                ],
                'day' => [
                    'orders_count' => $this->orders()->whereBetween('orders.created_at', [$todayStart, $todayEnd])->sum('order_product.quantity'),
                    'pending_orders_count' => $this->orders()->where('status', 'pending')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->sum('order_product.quantity'),
                    'confirmed_orders_count' => $this->orders()->where('status', 'confirmed')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->sum('order_product.quantity'),
                    'canceled_orders_count' => $this->orders()->where('status', 'canceled')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->sum('order_product.quantity'),
                    'shipped_orders_count' => $this->orders()->where('status', 'shipped')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->sum('order_product.quantity'),
                    'returned_orders_count' => $this->orders()->where('status', 'returned')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->sum('order_product.quantity'),
                    'delivered_orders_count' => $this->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->sum('order_product.quantity'),

                ],
                'week' => [
                    'orders_count' => $this->orders()->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])->sum('order_product.quantity'),
                    'pending_orders_count' => $this->orders()->where('status', 'pending')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->sum('order_product.quantity'),
                    'confirmed_orders_count' => $this->orders()->where('status', 'confirmed')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->sum('order_product.quantity'),
                    'canceled_orders_count' => $this->orders()->where('status', 'canceled')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->sum('order_product.quantity'),
                    'shipped_orders_count' => $this->orders()->where('status', 'shipped')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->sum('order_product.quantity'),
                    'returned_orders_count' => $this->orders()->where('status', 'returned')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->sum('order_product.quantity'),
                    'delivered_orders_count' => $this->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->sum('order_product.quantity'),

                ],
                'month' => [
                    'orders_count' => $this->orders()->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])->sum('order_product.quantity'),
                    'pending_orders_count' => $this->orders()->where('status', 'pending')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->sum('order_product.quantity'),
                    'confirmed_orders_count' => $this->orders()->where('status', 'confirmed')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->sum('order_product.quantity'),
                    'canceled_orders_count' => $this->orders()->where('status', 'canceled')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->sum('order_product.quantity'),
                    'shipped_orders_count' => $this->orders()->where('status', 'shipped')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->sum('order_product.quantity'),
                    'returned_orders_count' => $this->orders()->where('status', 'returned')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->sum('order_product.quantity'),
                    'delivered_orders_count' => $this->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->sum('order_product.quantity'),

                ],
                'year' => [
                    'orders_count' => $this->orders()->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])->sum('order_product.quantity'),
                    'pending_orders_count' => $this->orders()->where('status', 'pending')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->sum('order_product.quantity'),
                    'confirmed_orders_count' => $this->orders()->where('status', 'confirmed')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->sum('order_product.quantity'),
                    'canceled_orders_count' => $this->orders()->where('status', 'canceled')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->sum('order_product.quantity'),
                    'shipped_orders_count' => $this->orders()->where('status', 'shipped')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->sum('order_product.quantity'),
                    'returned_orders_count' => $this->orders()->where('status', 'returned')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->sum('order_product.quantity'),
                    'delivered_orders_count' => $this->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->sum('order_product.quantity'),

                ],
            ],
            'translations' => $this->translations,
            'reviews_count' => $this->reviews()->count(),

        ];
    }
}
