<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Note;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\State;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Shipper;
use App\Models\CouponCode;
use App\Events\OrderPlaced;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use App\Jobs\SendOrderToDSGO;
use App\Models\StatusHistory;
use App\Jobs\SendOrderToNavex;
use App\Jobs\SendOrderToOnesta;
use App\Models\ShippingSetting;
use Illuminate\Support\Facades\DB;
use App\Imports\OrderUpdatesImport;
use App\Imports\ExcelOrderImport;
use App\Imports\UpdateAmountImport;
use Illuminate\Support\Facades\Log;
use App\Imports\UpdatePendingImport;
use App\Imports\UpdateShipperImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\OrderResource;
use App\Http\Resources\StateResource;
use App\Jobs\SendOrderToDsgoBenArous;
use App\Http\Requests\PlaceOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Jobs\SendFacebookConversionEvent;
use App\Imports\UpdateDeliveryDatesImport;
use App\Jobs\SendGoogleAnalyticsPurchaseEvent;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateOrderProductsRequest;
use App\Http\Requests\StoreDeliveryDateImportRequest;
use Maatwebsite\Excel\Validators\ValidationException as ValidatorsValidationException;

class OrderController extends Controller
{
    protected $states;
    protected $shippers;
    protected $couponCodes;
    protected $messages = [
        'ar' => [
            'file.mimes' => 'الرجاء اختيار ملف بصيغة xls او xlsx',
            'date.date' => 'حقل التاريخ يجب أن يكون تاريخا',
            'date.after' => 'التاريخ يجب أن يكون في المستقبل',
        ],
        'fr' => [
            'file.mimes' => 'Veuillez choisir un fichier xls ou xlsx',
            'date.date' => 'Le champ date doit être une date valide',
            'date.after' => 'La date doit être dans le futur',
        ],
        'en' => [
            'file.mimes' => 'You need to choose an xls or xlsx file',
            'date.date' => 'The date field must be a valid date',
            'date.after' => 'The date must be in the future',

        ],

    ];
    public function __construct()
    {
        $this->states = State::all();
        $this->shippers = Shipper::all();
        $this->couponCodes = CouponCode::all();
    }


    public function import()
    {
        return Inertia::render('Admin/Orders/Import');
    }

    public function storeImport(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:xlsx,xls']
        ], $this->messages[app()->getLocale()]);

        $import = new OrderUpdatesImport();
        Excel::import($import, $request->file('file'));

        return redirect()->back();
    }

    public function excelImport()
    {
        return Inertia::render('Admin/Orders/ExcelImport');
    }

    public function storeExcelImport(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:xlsx,xls']
        ], $this->messages[app()->getLocale()]);

        $import = new ExcelOrderImport();
        Excel::import($import, $request->file('file'));

        return redirect()->back();
    }

    public function DeliveryDateImport()

    {
        return Inertia::render('Admin/Orders/DeliveryDateImport');
    }

    public function DeliveryDateStoreImport(Request $request)

    {


        $request->validate([
            'file' => ['required', 'mimes:xlsx,xls'],
            'date' => ['required', 'date']
        ], $this->messages[app()->getLocale()]);
        $import = new UpdateDeliveryDatesImport($request->date);
        Excel::import($import, $request->file('file'));

        return redirect()->back();
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Order::query();

        if ($request->filled('free_shipping')) {
            $freeShipping = $request->free_shipping;

            $freeShipping = array_map(function ($val) {
                return filter_var($val, FILTER_VALIDATE_BOOLEAN);
            }, $freeShipping);


            if (in_array(true, $freeShipping) && !in_array(false, $freeShipping)) {
                $query->where('free_shipping', true);
            } elseif (in_array(false, $freeShipping) && !in_array(true, $freeShipping)) {
                $query->where('free_shipping', false);
            }
        }


        if ($request->filled('search')) {
            $query->where('id', 'like', "%{$request->search}%");
        }

        $query->whereDoesntHave('nrp');

        if ($request->filled('clientPhone')) {
            $query->where(function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->clientPhone . '%')
                    ->orWhere('phone2', 'like', '%' . $request->clientPhone . '%');
            });
        }

        if ($request->filled('clientName')) {
            $query->where('client_name', 'like', "%{$request->clientName}%");
        }

        if ($request->filled('state')) {
            $query->where('state_id',  $request->state);
        }
        if ($request->filled('shipper')) {
            $query->where('shipper_id',  $request->shipper);
        }
        if ($request->filled('statuses')) {
            $query->whereIn('status',  $request->statuses);
        }
        if ($request->filled('minAmount')) {
            $query->where('amount', '>=', ($request->minAmount * 1000));
        }
        if ($request->filled('maxAmount')) {
            $query->where('amount', '<=', ($request->maxAmount * 1000));
        }

        if ($request->filled('discounted')) {
            $discounted = $request->discounted;

            if (in_array("true", $discounted) && !in_array("false", $discounted)) {
                $query->whereNotNull('coupon_code_id');
            } elseif (in_array("false", $discounted) && !in_array("true", $discounted)) {
                $query->whereNull('coupon_code_id');
            }
        }
        if ($request->filled('couponCode')) {
            $query->where('coupon_code_id',   $request->couponCode);
        }


        if ($request->filled('minDeliveryDate')) {
            $query->whereDate('delivery_date', '>=', $request->minDeliveryDate);
        }

        if ($request->filled('maxDeliveryDate')) {
            $maxDateTime = \Carbon\Carbon::parse($request->maxDeliveryDate)->endOfDay();
            $query->where('delivery_date', '<=', $maxDateTime);
        }

        if ($request->filled('minCreationDate')) {
            $query->whereDate('created_at', '>=', $request->minCreationDate);
        }

        if ($request->filled('maxCreationDate')) {
            $maxDateTime = \Carbon\Carbon::parse($request->maxCreationDate)->endOfDay();
            $query->where('created_at', '<=', $maxDateTime);
        }



        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'highest_amount':
                    $query->orderBy('amount', 'desc');
                    break;

                case 'lowest_amount':
                    $query->orderBy('amount', 'asc');
                    break;

                case 'newest_delivery_date':
                    $query->orderBy('delivery_date', 'desc');
                    break;

                case 'oldest_delivery_date':
                    $query->orderBy('delivery_date', 'asc');
                    break;

                case 'newest_creation_date':
                    $query->orderBy('created_at', 'desc');
                    break;

                case 'oldest_creation_date':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }


        $orders = $query->paginate(20)->withQueryString();


        return Inertia::render('Admin/Orders/Index', [
            'orders' => OrderResource::collection($orders),
            'filters' => $request->all(),
            'states' => $this->states,
            'shippers' => $this->shippers,
            'couponCodes' => $this->couponCodes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all()
            ->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'type' => $product->type,
                'price' => $product->getFormattedPrice(),
                'main_image_url' => $product->wrappers()->first()->getFirstMediaUrl('images') ?: asset('storage/products/product.webp'),
                'translations' => $product->translations
            ]);

        return Inertia::render('Orders/Create', [
            'products' => $products,

            'states' =>  StateResource::collection(State::all()),
            'couponCodes' => CouponCode::where('status', 'active')->get(),
            'shippers' => $this->shippers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeAbandoned(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'digits:8', 'regex:/^[234579]\d{7}$/'],
            'name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:600'],
            'state' => ['nullable', 'array'],
            'state.id' => ['nullable', 'exists:states,id'],
            'cart' => ['nullable', 'array'],
            'cart.*.id' => ['nullable', 'exists:products,id'],
            'cart.*.quantity' => ['nullable', 'numeric', 'min:.5'],
            'cart.*.price' => ['nullable', 'numeric'],
            'total' => ['nullable', 'numeric'],
            'free_shipping' => ['nullable', 'boolean'],
            'coupon_code' => ['nullable', 'exists:coupon_codes,id']
        ]);

        $cart = $validated['cart'] ?? [];
        $products = $this->getProducts($cart);

        $client = $this->getClient($request, $validated['state']['id'] ?? null);

        $clientName = $validated['name'] ?? $client->name;
        $clientAddress = $validated['address'] ?? $client->address;
        $clientStateId = $validated['state']['id'] ?? $client->state_id;

        if (!empty($validated['name'])) {
            $client->name = $validated['name'];
        }

        if (!empty($validated['address'])) {
            $client->address = $validated['address'];
        }

        if ($clientStateId) {
            $client->state_id = $clientStateId;
        }

        $client->save();

        $abandonedOrder = Order::where('phone', $request->phone)
            ->where('status', 'abandoned')
            ->latest('id')
            ->first();

        $shippingCost = 0;
        $stateId = $clientStateId;

        if ($stateId) {
            $shippingState = State::find($stateId);
            $freeShipping = (bool) ($validated['free_shipping'] ?? false);

            if (!$freeShipping && $shippingState) {
                $shippingCost = (int) $shippingState->shipping_cost;
            }
        }

        $amount = !empty($validated['total'])
            ? (int) ($validated['total'] * 1000)
            : null;

        $state = State::find($stateId);
        $defaultShipperId =  $state->default_shipper_id ? $state->default_shipper_id : null;

        $orderData = [
            'client_id' => $client->id,
            'phone' => $request->phone,
            'client_name' => $clientName,
            'address' => $clientAddress,
            'state_id' => $stateId,
            'shipping_cost' => $shippingCost,
            'free_shipping' => (bool) ($validated['free_shipping'] ?? false),
            'amount' => $amount,
            'note' => $validated['note'] ?? null,
            'delivery_date' => $this->getDeliveryDate($stateId),
            'coupon_code_id' => $validated['coupon_code'] ?? null,
            'status' => 'abandoned',
            'source' => 'client',
            'shipper_id' => $defaultShipperId,
        ];

        if ($abandonedOrder) {
            $abandonedOrder->update($orderData);
            if (!empty($cart)) {
                $abandonedOrder->products()->sync($products);
            }

            return response()->json($abandonedOrder->fresh());
        }

        $order = Order::create($orderData);

        if (!empty($cart)) {
            $order->products()->attach($products);
        }

        event(new OrderPlaced($order));

        return response()->json($order);
    }

    public function place(PlaceOrderRequest $request)
    {
        $validated = $request->validated();


        $coupon = $validated['coupon'] ?? null;

        $cart = $request->cart;

        $products = $this->getProducts($cart);

        $state = $validated['state'] ?? null;
        $stateId = $state['id'] ?? 1;
        $deliveryDate = $this->getDeliveryDate($stateId);
        $client = $this->getClient($request, $stateId);

        // Check if form-level flag is set OR if any item in cart has free_shipping
        $freeShipping = (bool) ($validated['free_shipping'] ?? false);
        if (!$freeShipping && is_array($cart)) {
            $freeShipping = collect($cart)->some(function ($item) {
                return isset($item['free_shipping']) && $item['free_shipping'] === true;
            });
        }

        $abandonedOrder = Order::where('phone', $request->phone)
            ->where('status', 'abandoned')
            ->latest('id')
            ->first();

        $order = $abandonedOrder;

        if (!$order) {
            $order = new Order();
        }
        $state = State::find($stateId);
        $defaultShipperId =  $state->default_shipper_id ? $state->default_shipper_id : null;


        $order->fill([
            'client_id' => $client->id,
            'phone' => $request->phone,
            'client_name' => $request->name ?? $client->name,
            'address' => $request->address ?? $client->address,
            'state_id' => $stateId,
            'shipping_cost' => $freeShipping
                ? 0
                : (int) ($state['shipping_cost'] ?? State::find(1)->shipping_cost),
            'free_shipping' => $freeShipping,
            'amount' => isset($validated['total']) ? $validated['total'] * 1000 : null,
            'note' => $validated['note'] ?? null,
            'delivery_date' => $deliveryDate,
            'coupon_code_id' => $coupon ? $coupon['id'] : null,
            'status' => 'pending',
            'source' => 'client',
            'shipper_id' => $defaultShipperId,
        ]);

        $order->save();



        if ($client->state_id != $stateId) {
            $client->state_id = $stateId;
            $client->city_id = null;
            $client->locality_id = null;
        }

        if (!empty($request->name)) {
            $client->name = $request->name;
        }

        if (!empty($request->address)) {
            $client->address = $request->address;
        }

        $client->save();
        $order->products()->sync($products);

        event(new OrderPlaced($order));

        // Send Facebook Conversions API Purchase event
        $this->sendFacebookPurchaseEvent($order, $request, $cart);

        // Send Google Analytics 4 Purchase event
        $this->sendGoogleAnalyticsPurchaseEvent($order, $request, $cart);

        return redirect()->back()->with([
            'order_conversion_data' => [
                'transaction_id' => $order->id,
                'value' => $order->amount / 1000,
                'currency' => 'TND',
            ]
        ]);
    }
    public function store(StoreOrderRequest $request)
    {

        // dd($request->all());
        $validated = $request->validated();
        $coupon = $validated['coupon'] ?? null;
        $shipper = $validated['shipper'];
        $cart = $request->cart;
        $products = $this->getProducts($cart);
        $deliveryDate = $validated['deliveryDate'];
        $client = $this->getClient($request, $validated['state'], $request->client_id);
        if ($request->client_id) {

            $client->update([
                'state_id' => $request->state,
                'city_id' => $request->city,
                'locality_id' => $request->locality,
                'name' => $request->name,
                'phone' => $request->phone,
                'phone2' => $request->phone2,
                'address' => $request->address,
            ]);
        }
        $order = Order::create([
            'client_id' => $client->id,
            'phone' => $request->phone,
            'phone2' => $request->phone2 ?? null,
            'client_name' => $request->name,
            'address' => $request->address,
            'state_id' => $request->state,
            'city_id' => $request->city,
            'locality_id' => $request->locality,
            'shipping_cost' => (int) ($request->shipping_cost * 1000),
            'free_shipping' => (bool) $validated['free_shipping'],
            'amount' => $validated['total'] * 1000,
            'note' => null,
            'delivery_date' => $deliveryDate,
            'coupon_code_id' => $coupon,
            'shipper_id' => $shipper,
            'status' => $validated['status'],
            'source' => 'admin',
        ]);

        if ($validated['nrp'] && $validated['status'] == 'pending') {
            $order->nrp()->create([
                'tries' => 1
            ]);
        }

        $order->products()->attach($products);



        if ($validated['status'] == 'confirmed') {
            $order->load(['state.translations', 'city.translations', 'locality.translations']);

            if (strtolower($order->shipper->name) == 'navex') {
                dispatch(new SendOrderToNavex($order));
            }
            if (strtolower($order->shipper->name) == 'dsgo') {
                dispatch(new SendOrderToDSGO($order));
            }
            if (strtolower($order->shipper->name) == 'dsgo ben arous') {
                dispatch(new SendOrderToDsgoBenArous($order));
            }
            if (strtolower($order->shipper->name) == 'onesta') {
                dispatch(new SendOrderToOnesta($order));
            }
        }

        if ($validated['note']) {
            Note::create([
                'content' => $validated['note'],
                'notable_id' => $order->id,
                'notable_type' => "App\Models\Order",
                'user_id' => $request->user()->id
            ]);
        }
        return redirect()->back();
    }

    public function editProducts(Order $order)
    {
        $products = Product::all()
            ->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'type' => $product->type,
                'price' => $product->getFormattedPrice(),

                'main_image_url' => $product->wrappers()->first()->getFirstMediaUrl('images') ?: asset('storage/products/product.webp'),
                'translations' => $product->translations
            ]);
        return Inertia::render('Orders/EditProducts', ['order' => $order, 'products' => $products]);
    }
    public function updateProducts(UpdateOrderProductsRequest $request)
    {
        $validated = $request->validated();
        $order = Order::find($validated['order_id']);

        if (!$order) {
            return abort(404);
        }

        $shipping_cost = $order->shipping_cost;
        $amount = ($validated['total'] * 1000) + $shipping_cost; // Assuming * 1000 is for cent/paisa conversion

        $cart = $request->cart;
        $products = $this->getProducts($cart); // Make sure getProducts() is defined or adjust

        DB::transaction(function () use ($order, $amount, $products) {
            $order->update([
                'amount' => $amount,
            ]);

            $order->products()->detach();

            $order->products()->attach($products);
        });
        return redirect()->route('orders.show', $order);
    }

    /**
     * Display the specified resource.
     */

    public function show(Order $order)
    {
        $order->load([
            'client.orders' => fn($query) => $query->latest('created_at')->take(5),
            'products',
            'notes.user',
            'activities.causer',
            'state',
            'city',
            'locality',
            'couponCode',
            'shipper',
            'nrp',
        ]);

        return Inertia::render('Admin/Orders/Show', ['order' => new OrderResource($order)]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        return Inertia::render('Admin/Orders/Edit', [
            'order' => new OrderResource($order),
            'shippers' => $this->shippers,
            'states' => $this->states
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $state = State::find($request->state);

        $oldShippingCost = $order->shipping_cost;
        $newShippingCost = $state->shipping_cost;
        if ($request->free_shipping) {
            $newShippingCost = 0;
        }
        if ($newShippingCost !== $oldShippingCost) {

            $newAmount = ($order->amount - $oldShippingCost) + $newShippingCost;

            $order->amount = $newAmount;
            $order->shipping_cost = $newShippingCost;
        }

        $order->shipper_id = $request->shipper;
        $order->client_name = $request->name;
        $order->phone = $request->phone;
        $order->phone2 = $request->phone2;
        $order->address = $request->address;
        $order->delivery_date = Carbon::parse($request->deliveryDate)->format('Y-m-d');
        $order->state_id = $request->state;
        $order->city_id = $request->city;
        $order->locality_id = $request->locality;
        $order->free_shipping = $request->free_shipping;

        $order->save();

        $order->client()->update([
            'state_id' => $request->state,
            'city_id' => $request->city,
            'locality_id' => $request->locality,
            'name' => $request->name,
            'phone' => $request->phone,
            'phone2' => $request->phone2,
            'address' => $request->address,
        ]);

        return redirect()->route('orders.show', $order);
    }


    public function updateStatus(Request $request,  Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:pending,shipped,delivered,returned,confirmed,canceled,abandoned']
        ]);


        $order->update([
            'status' => $request->status
        ]);

        $order->nrp()->delete();


        if ($request->status == 'confirmed') {
            $order->load(['state.translations', 'city.translations', 'locality.translations']);

            if ($order->shipper->name == 'Navex') {
                dispatch(new SendOrderToNavex($order));
            }
            if ($order->shipper->name == 'DSGO') {
                dispatch(new SendOrderToDSGO($order));
            }
            if (strtolower($order->shipper->name) == 'dsgo ben arous') {
                dispatch(new SendOrderToDsgoBenArous($order));
            }
            if ($order->shipper->name == 'Onesta') {
                dispatch(new SendOrderToOnesta($order));
            }
        }


        return redirect()->back();
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {

        try {
            DB::beginTransaction();

            $products = OrderProduct::where('order_id', $order->id)->get();

            foreach ($products as $product) {
                $product->delete();
            }



            $invoices = Invoice::where('invoiceable_type', 'App\Models\Order')->where('invoiceable_id', $order->id)->get();
            foreach ($invoices as $invoice) {
                $invoice->delete();
            }

            if ($order->nrp()->exists()) {
                $order->nrp()->delete();
            }

            if ($order->client->deleted_at == null) {
                $order->delete();
            } else {
                if ($order->client->orders()->count() == 1) {
                    $client = $order->client;
                    $order->forceDelete();
                    $client->forceDelete();
                } else {
                    $order->forceDelete();
                }
            }


            DB::commit();


            return redirect()->route('orders.index');
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error($e->getMessage());
            abort(500);
        }
    }

    public function printInvoice(Order $order)
    {
        $order->load(['client', 'state', 'city', 'locality', 'products']);

        return view('single-invoice', ['order' => $order]);
    }


    private function getClient($request, $state_id, $client_id = null)
    {

        if ($client_id) {
            $client = Client::find($client_id);
            return $client;
        }

        $existingClient = Client::where('phone', $request->phone)->first();

        if ($existingClient) {
            return $existingClient;
        }

        $clientName = filled($request->name) ? trim($request->name) : 'client';
        $clientAddress = filled($request->address) ? trim($request->address) : 'N.D';

        $state_id = $state_id ?? 1;

        return Client::create([
            'name' => $clientName,
            'address' => $clientAddress,
            'state_id' => $state_id,
            'phone' => $request->phone,
            'phone2' => $request->phone2,
            'city_id' => $request->city ?? null,
            'locality_id' => $request->locality ?? null,
        ]);
    }

    private function getDeliveryDate($state_id)
    {
        if (!$state_id) {
            return null;
        }

        $state = State::find($state_id);
        $shippingSettings = ShippingSetting::first();
        $deliveryDate = null;

        if (in_array($state->translate('fr')->name, ['Tunis', 'Ariana', 'Ben Arous', 'Manouba'])) {
            $deliveryDate = $shippingSettings->tunis_acceptance_delivery_date;
        } else {
            $deliveryDate = $shippingSettings->wilayet_acceptance_delivery_date;
        }

        return $deliveryDate;
    }

    private function getProducts($cart)
    {
        $products = collect($cart)->mapWithKeys(function ($product) {
            return [
                $product['id'] => [
                    'quantity' => $product['quantity'],
                    'price' => (int) ($product['price'] * 1000),
                    'sub_total' =>  $product['quantity'] * (int) ($product['price'] * 1000)
                ],
            ];
        });

        return $products;
    }

    /**
     * Send Facebook Conversions API Purchase event
     */
    private function sendFacebookPurchaseEvent(Order $order, Request $request, array $cart): void
    {
        try {
            // Prepare order data for Facebook Conversions API
            $orderData = [
                'order_id' => $order->id,
                'total' => $order->amount / 1000, // Convert from millicents to TND
                'product_ids' => collect($cart)->pluck('id')->toArray(),
                'source_url' => $request->header('referer') ?? url()->current(),
            ];

            // Prepare user data for better matching (without hashing)
            $userData = [
                'phone' => $request->phone,
                'first_name' => $this->extractFirstName($request->name ?? ''),
                'last_name' => $this->extractLastName($request->name ?? ''),
                'state' => optional($order->state)->translate('en')->name ?? 'Unknown',
                'country' => 'Tunisia',
                'client_ip_address' => $request->ip(),
                'client_user_agent' => $request->userAgent(),
            ];



            // Dispatch the job asynchronously
            SendFacebookConversionEvent::dispatch('Purchase', $orderData, $userData);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch Facebook Purchase event', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send Google Analytics 4 Purchase event
     */
    private function sendGoogleAnalyticsPurchaseEvent(Order $order, Request $request, array $cart): void
    {
        try {
            // prepare items
            $items = collect($cart)->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['translations'][2]['name'] ?? null,
                    'quantity' => (int) $item['quantity'],
                ];
            })->toArray();

            $orderData = [
                'order_id' => $order->id,
                'total' => $order->amount / 1000,
                'items' => $items,
            ];

            // Extract generic GA Client ID
            // Use $_COOKIE directly because Laravel's EncryptCookies middleware will ignore/discard 
            // the unencrypted JS-set cookies unless configured otherwise.
            $clientId = $_COOKIE['_ga'] ?? null;
            if ($clientId) {
                // Format is usually GA1.x.CLIENT_ID. We want the CLIENT_ID part.
                $parts = explode('.', $clientId);
                if (count($parts) >= 4) {
                    $clientId = implode('.', array_slice($parts, 2));
                }
            } else {
                $clientId = (string) \Illuminate\Support\Str::uuid();
            }

            // Extract Session ID from _ga_<MEASUREMENT_ID>
            $measurementId = config('services.google_analytics.measurement_id', 'G-S9JRM4LHNP');
            $measurementIdClean = str_replace('G-', '', $measurementId);
            $sessionIdCookieName = '_ga_' . $measurementIdClean;
            $sessionIdFull = $_COOKIE[$sessionIdCookieName] ?? null;
            $sessionId = null;

            if ($sessionIdFull) {
                // Format: GS1.1.1695299443.... (Old)
                // Format: GS2.1.s1765363655$o1... (New/Consent Mode)
                $parts = explode('.', $sessionIdFull);
                if (count($parts) >= 3) {
                    $rawSid = $parts[2];
                    // Extract the first sequence of 9-10 digits (the timestamp)
                    if (preg_match('/(\d{9,10})/', $rawSid, $matches)) {
                        $sessionId = $matches[1];
                    } else {
                        $sessionId = $rawSid;
                    }
                }
            }

            Log::info('GA4 Debug: IDs extracted', [
                'client_id' => $clientId,
                'session_id_raw' => $sessionIdFull,
                'session_id_extracted' => $sessionId
            ]);

            $debugMode = config('app.debug') || $request->has('debug_mode');

            SendGoogleAnalyticsPurchaseEvent::dispatch(
                $orderData,
                $clientId,
                $sessionId,
                $debugMode
            );
        } catch (\Exception $e) {
            Log::error('Failed to dispatch GA4 Purchase event', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Extract first name from full name
     */
    private function extractFirstName(?string $fullName): string
    {
        $fullName = trim((string) $fullName);
        $parts = explode(' ', $fullName);
        return $parts[0] ?? 'Client';
    }

    /**
     * Extract last name from full name
     */
    private function extractLastName(?string $fullName): string
    {
        $fullName = trim((string) $fullName);
        $parts = explode(' ', $fullName);
        return count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
    }


    public function actions()
    {
        return Inertia::render('Admin/Orders/Actions');
    }

    public function shipperUpdateImport()
    {
        return Inertia::render('Admin/Orders/ShipperUpdateImport');
    }

    public function shipperUpdateStoreImport(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:xlsx,xls']
        ], $this->messages[app()->getLocale()]);

        $import = new UpdateShipperImport();
        Excel::import($import, $request->file('file'));

        return redirect()->back();
    }

    public function amountUpdateImport()
    {
        return Inertia::render('Admin/Orders/AmountUpdateImport');
    }

    public function amountUpdateStoreImport(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:xlsx,xls']
        ], $this->messages[app()->getLocale()]);

        $import = new UpdateAmountImport();
        Excel::import($import, $request->file('file'));

        return redirect()->back();
    }
    public function pendingUpdateImport()
    {
        return Inertia::render('Admin/Orders/PendingUpdateImport');
    }

    public function pendingUpdateStoreImport(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:xlsx,xls']
        ], $this->messages[app()->getLocale()]);

        $import = new UpdatePendingImport();
        Excel::import($import, $request->file('file'));

        return redirect()->back();
    }
}
