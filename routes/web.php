<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CouponCodeController;
use App\Http\Controllers\FrontEnd\FrontEndController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NRPController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipperController;
use App\Http\Controllers\ShippingReportController;
use App\Http\Controllers\SellingReportController;
use App\Http\Controllers\ShippingSettingController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TopBarSettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WrapperController;
use App\Http\Middleware\isActiveMiddleware;
use App\Http\Middleware\isAdminMiddleware;
use App\Http\Middleware\IsBannedMiddleware;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

Route::get('banned', function () {
    return   Inertia::render('UserBanned');
})->name('user.banned')->middleware(IsBannedMiddleware::class);
Route::middleware(['auth', isActiveMiddleware::class])->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');


    Route::delete('products/{product}/AllMedia', [ProductController::class, 'deleteAllMedia'])->name('products.deleteAllMedia');
    Route::delete('media/{media}', [ProductController::class, 'deleteMedia'])->name('products.deleteMedia');
    Route::post('products/{product}/media/', [ProductController::class, 'storeMedia'])->name('products.media');
    Route::resource('products', ProductController::class);

    Route::resource('wrappers', WrapperController::class);

    Route::resource('shippers', ShipperController::class)->except(['show'])->middleware(isAdminMiddleware::class);
    Route::resource('states', StateController::class)->only(['edit', 'index', 'update'])->middleware(isAdminMiddleware::class);

    Route::resource('settings', TopBarSettingController::class)->only(['index', 'update']);

    Route::resource('coupons', CouponCodeController::class)->except('show')->middleware(isAdminMiddleware::class);
    Route::get('clients/import', [ClientController::class, 'import'])->name('clients.import.create');
    Route::post('clients/import', [ClientController::class, 'storeImport'])->name('clients.import.store');
    Route::resource('clients', ClientController::class);

    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('orders/updates/import', [OrderController::class, 'import'])->name('orders.import.create');
    Route::post('orders/updates/import', [OrderController::class, 'storeImport'])->name('orders.import.store');
    Route::get('orders/import', [OrderController::class, 'excelImport'])->name('orders.excel.import.create');
    Route::post('orders/import', [OrderController::class, 'storeExcelImport'])->name('orders.excel.import.store');
    Route::get('orders/actions', [OrderController::class, 'actions'])->name('ordersActions');
    Route::resource('orders', OrderController::class);
    Route::get('orders/{order}/update/products', [OrderController::class, 'editProducts'])->name('orders.editProducts');
    Route::post('orders/update/products', [OrderController::class, 'updateProducts'])->name('orders.updateProducts');
    Route::get('orders/delivery_dates/updates/import', [OrderController::class, 'DeliveryDateImport'])->name('orders.deliveryDate.import.create');
    Route::post('orders/delivery_dates/updates/import', [OrderController::class, 'DeliveryDateStoreImport'])->name('orders.deliveryDate.import.store');
    Route::get('orders/shipper/updates/import', [OrderController::class, 'shipperUpdateImport'])->name('orders.shipperUpdate.import.create');
    Route::post('orders/shipper/updates/import', [OrderController::class, 'shipperUpdateStoreImport'])->name('orders.shipperUpdate.import.store');
    Route::get('orders/amount/updates/import', [OrderController::class, 'amountUpdateImport'])->name('orders.amountUpdate.import.create');
    Route::post('orders/amount/updates/import', [OrderController::class, 'amountUpdateStoreImport'])->name('orders.amountUpdate.import.store');
    Route::get('orders/pending/updates/import', [OrderController::class, 'pendingUpdateImport'])->name('orders.pendingUpdate.import.create');
    Route::post('orders/pending/updates/import', [OrderController::class, 'pendingUpdateStoreImport'])->name('orders.pendingUpdate.import.store');

    Route::get('/orders/{order}/print-invoice', [OrderController::class, 'printInvoice']);


    Route::get('/shipping-reports/download-excel/{id}', [ShippingReportController::class, 'downloadExcel'])->name('shipping-reports.download-excel');
    Route::get('/shipping-reports/download-pdf/{id}', [ShippingReportController::class, 'downloadPdf'])->name('shipping-reports.download-pdf');
    Route::resource('shipping_reports', ShippingReportController::class)->except(['show', 'edit', 'update']);

    Route::get('/selling-reports/download-excel/{id}', [SellingReportController::class, 'downloadExcel'])->name('selling-reports.download-excel');
    Route::get('/selling-reports/download-pdf/{id}', [SellingReportController::class, 'downloadPdf'])->name('selling-reports.download-pdf');
    Route::resource('selling_reports', SellingReportController::class)->except(['show', 'edit', 'update']);

    Route::patch('notifications/{notification_id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::patch('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::get('notifications/{user}/get', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::resource('notifications', NotificationController::class)->only(['index']);

    Route::resource('invoices', InvoiceController::class)->middleware(isAdminMiddleware::class);
    Route::resource('employees', UserController::class)->middleware(isAdminMiddleware::class);


    Route::get('statistics/earnings', [StatisticsController::class, 'earnings'])->name('stats.earnings');
    Route::get('statistics/orders', [StatisticsController::class, 'orders'])->name('stats.orders');
    Route::get('statistics/products', [StatisticsController::class, 'products'])->name('stats.products');
    Route::get('statistics/clients', [StatisticsController::class, 'clients'])->name('stats.clients');
    Route::get('statistics/couponCodes', [StatisticsController::class, 'couponCodes'])->name('stats.couponCodes');

    Route::resource('notes', NoteController::class);
    Route::get('notes/newNote/{id}/{type}', [NoteController::class, 'newNote'])->name('notes.newNote');

    Route::get('shippingSettings/{id}', [ShippingSettingController::class, 'edit'])->name('shippingSettings.edit');
    Route::put('shippingSettings/{id}', [ShippingSettingController::class, 'update'])->name('shippingSettings.update');

    Route::post('nrp/order{order}', [NRPController::class, 'store'])->name('nrp.store');
    Route::get('nrp', [NRPController::class, 'index'])->name('nrp.index');

    Route::delete('activity-log/clean', function () {
        $exitCode = \Illuminate\Support\Facades\Artisan::call('activitylog:clean', [
            '--force' => true,
            '--days' => config('activitylog.delete_records_older_than_days'),
        ]);

        if ($exitCode !== 0) {
            return redirect()->back()->with('error', 'Failed to clean activity log.');
        }

        return redirect()->back()->with('success', 'Activity log cleaned successfully.');
    })->name('activitylog.clean');

    Route::get('/states/{state}/cities', [LocationController::class, 'cities'])->name('cities.index');
    Route::get('/cities/{city}/localities', [LocationController::class, 'localities'])->name('localities.index');
    Route::get('/clients/{phone}/search', [ClientController::class, 'searchByPhone'])->name('clients.searchByPhone');
});
Route::name('FE.')->group(function () {


    Route::get('/', [FrontEndController::class, 'home'])->name('home');

    Route::get('/about', [FrontEndController::class, 'about'])->name('about');
    Route::get('/cart', [FrontEndController::class, 'cart'])->name('cart');
    Route::get('/checkout', [FrontEndController::class, 'checkout'])->name('checkout');
    Route::get('/contact', [FrontEndController::class, 'contact'])->name('contact');
    Route::get('/terms', [FrontEndController::class, 'terms'])->name('terms');
    Route::get('/privacy', [FrontEndController::class, 'privacy'])->name('privacy');
    Route::get('/shop/{wrapper:slug}', [FrontEndController::class, 'wrapper'])->name('wrapper');
    Route::get('/shop', [FrontEndController::class, 'shop'])->name('shop');
    Route::post('getCode', [CouponCodeController::class, 'getCode'])->name('codes.getCode');
    Route::post('order/place', [OrderController::class, 'place'])->name('orders.place');
    Route::post('order/abandoned', [OrderController::class, 'storeAbandoned'])->name('orders.abandoned');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar', 'fr'])) {
        Session::put('locale', $locale);
        redirect()->back();
    }
    redirect()->back();
});
