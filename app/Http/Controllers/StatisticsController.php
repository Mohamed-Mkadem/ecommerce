<?php

namespace App\Http\Controllers;

use App\Http\Resources\CouponCodeStatisticResource;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductStatisticsResource;
use App\Http\Resources\StatisticsProductResource;
use App\Models\CouponCode;

class StatisticsController extends Controller
{

    public function earnings()
    {

        $expensesCategories = Invoice::getExpensesCategories();
        $revenuesCategories = Invoice::getRevenuesCategories();
        $earnings = Invoice::getEarnings(true);
        return Inertia::render(
            'Admin/Statistics/Earnings',
            [
                'earnings' => $earnings,
                'expensesCategories' => $expensesCategories,
                'revenuesCategories' => $revenuesCategories
            ]
        );
    }

    public function orders()
    {
        $orders = Order::getCount();

        $states = Order::getOrdersByStates();
        return Inertia::render('Admin/Statistics/Orders', ['orders' => $orders, 'states' => $states]);
    }

    public function products()
    {

        $bestSelling = Product::getBestSelling();
        return Inertia::render('Admin/Statistics/Products', [
            'statistics' => ProductStatisticsResource::collection(Product::paginate(20)),
            'bestSelling' => $bestSelling
        ]);
    }



    public function clients()
    {
        $clients = Client::getCount();
        $states = Client::getClientsByStates();

        return Inertia::render('Admin/Statistics/Clients', ['clients' => $clients, 'states' => $states]);
    }

    public function couponCodes()
    {

        $count = CouponCode::getCount();
        $couponCodeUsagePercentage = Order::couponCodeUsagePercentage();

        return Inertia::render('Admin/Statistics/CouponCodes', [
            'count' => $count,
            'usageStatistics' => $couponCodeUsagePercentage,
            'statistics' => CouponCodeStatisticResource::collection(CouponCode::paginate(20))

        ]);
    }
}
