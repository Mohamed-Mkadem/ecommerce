<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\State;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Imports\ClientImport;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\NoteResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\OrderResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ReviewResource;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientController extends Controller
{

    protected $states;
    protected $messages = [
        'ar' => ['file.mimes' => 'الرجاء اختيار ملف بصيغة xls او xlsx'],
        'fr' => ['file.mimes' => 'Veuillez choisir un fichier xls ou xlsx'],
        'en' => ['file.mimes' => 'You need to choose an xls or xlsx file'],

    ];
    public function __construct()
    {
        // Initialize the states collection in the constructor
        $this->states = State::all();
    }
    /**
     * Display a listing of the resource.
     */


    public function index(Request $request)
    {
        // Start with the base query for clients, selecting all client columns
        $baseClientQuery = Client::query()->select('clients.*');

        // Add scalar subqueries to calculate the total amount spent on delivered orders and total orders count.
        // These will become attributes on the Client model when fetched.
        $baseClientQuery->addSelect([
            'total_delivered_spent' => Order::selectRaw('COALESCE(SUM(amount), 0)')
                ->whereColumn('client_id', 'clients.id')
                ->where('status', 'delivered'),
            'total_orders_count' => Order::selectRaw('COALESCE(COUNT(*), 0)')
                ->whereColumn('client_id', 'clients.id')
        ]);

        // Apply basic search filters to the base query
        if ($request->filled('search')) {
            $baseClientQuery->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('phone')) {
            $baseClientQuery->where(function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->phone . '%')
                    ->orWhere('phone2', 'like', '%' . $request->phone . '%');
            });
        }

        if ($request->state != null) {
            $baseClientQuery->where('state_id', (int) $request->state);
        }

        // --- Create a derived table (subquery) for filtering and pagination ---
        $derivedTableQuery = DB::query()->fromSub($baseClientQuery, 'clients_with_aggregates');

        // Apply filters on the calculated columns to the derived table
        if ($request->filled('minSpent')) {
            $minSpent = ($request->input('minSpent') * 1000);
            $derivedTableQuery->where('total_delivered_spent', '>=', $minSpent);
        }

        if ($request->filled('maxSpent')) {
            $maxSpent = ($request->input('maxSpent') * 1000);
            $derivedTableQuery->where('total_delivered_spent', '<=', $maxSpent);
        }

        if ($request->filled('minOrdersCount')) {
            $minOrdersCount = $request->input('minOrdersCount');
            $derivedTableQuery->where('total_orders_count', '>=', $minOrdersCount);
        }

        if ($request->filled('maxOrdersCount')) {
            $maxOrdersCount = $request->input('maxOrdersCount');
            $derivedTableQuery->where('total_orders_count', '<=', $maxOrdersCount);
        }

        // Apply sorting to the derived table
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'lowest_spent':
                    $derivedTableQuery->orderBy('total_delivered_spent', 'asc');
                    break;
                case 'lowest_orders':
                    $derivedTableQuery->orderBy('total_orders_count', 'asc');
                    break;
                case 'highest_spent':
                    $derivedTableQuery->orderBy('total_delivered_spent', 'desc');
                    break;
                case 'highest_orders':
                    $derivedTableQuery->orderBy('total_orders_count', 'desc');
                    break;
                default:
                    $derivedTableQuery->orderBy('total_delivered_spent', 'desc');
                    break;
            }
        } else {
            $derivedTableQuery->orderBy('total_delivered_spent', 'desc');
        }

        // Paginate the results from the derived table.
        $paginatedResults = $derivedTableQuery->paginate(24)->withQueryString();

        // --- Re-hydrate results to Eloquent models ---
        $clientIds = $paginatedResults->pluck('id')->toArray();

        // IMPORTANT: Re-add the addSelect for total_delivered_spent and total_orders_count
        // when fetching the Eloquent models, so these attributes are available.
        $eloquentClientsQuery = Client::whereIn('id', $clientIds)
            ->with(['state', 'notes.user', 'reviews']);

        $eloquentClientsQuery->addSelect([
            'total_delivered_spent' => Order::selectRaw('COALESCE(SUM(amount), 0)')
                ->whereColumn('client_id', 'clients.id')
                ->where('status', 'delivered'),
            'total_orders_count' => Order::selectRaw('COALESCE(COUNT(*), 0)')
                ->whereColumn('client_id', 'clients.id')
        ]);

        $eloquentClients = $eloquentClientsQuery->get();


        // Sort the Eloquent models to match the order from the derived table's pagination.
        $eloquentClients = $eloquentClients->sortBy(function ($client) use ($clientIds) {
            return array_search($client->id, $clientIds);
        })->values();

        // Manually create a new LengthAwarePaginator with the Eloquent models
        $clients = new LengthAwarePaginator(
            $eloquentClients,
            $paginatedResults->total(),
            $paginatedResults->perPage(),
            $paginatedResults->currentPage(),
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Return Inertia response
        return Inertia::render('Admin/Clients/Index', [
            'clients' => ClientResource::collection($clients),
            'states' => $this->states,
            'filters' => $request->all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Clients/Create', [
            'states' => $this->states,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {


        Client::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'state_id' => $request->state,
            'phone2' => $request->phone2 ?? null,
            'city_id' => $request->city,
            'locality_id' => $request->locality,
        ]);

        return redirect()->back();
    }

    public function import()
    {
        return Inertia::render('Admin/Clients/Import');
    }
    public function storeImport(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:xlsx,xls']
        ], $this->messages[app()->getLocale()]);

        $import = new ClientImport();
        Excel::import($import, $request->file('file'));


        return redirect()->back();
    }

    public function show(Client $client)
    {
        $client->load(['state', 'locality.city']);
        $client->loadCount('orders', 'reviews');

        $deleted_orders_count = $client->orders()->where('deleted_at', '!=', null)->count();

        $spent = number_format($client->orders()->where('status', 'delivered')->sum('amount') / 1000, 3, '.', '');
        $orders = $client->orders()->paginate();

        return Inertia::render('Admin/Clients/Show', [
            'client' => $client,
            'orders' => OrderResource::collection($orders),
            'spent' => $spent,
            'notes' => NoteResource::collection($client->notes),
            'deleted_orders_count' => $deleted_orders_count
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return Inertia::render('Admin/Clients/Edit', ['client' => $client, 'states' => $this->states]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $client->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'state_id' => $request->state,
            'phone2' => $request->phone2 ?? null,
            'city_id' => $request->city,
            'locality_id' => $request->locality,
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {

        if ($client->orders()->exists()) {
            $client->delete();
        } else {
            $client->forceDelete();
        }

        return redirect()->route('clients.index');
    }

    public function searchByPhone($phone)
    {
        if (!$phone) {
            return response()->json([]);
        }
        $client = Client::where('phone', $phone)
            ->orWhere('phone2', $phone)
            ->with(['state', 'city', 'locality', 'orders' => function ($query) {
                $query->latest()->limit(5);
            }])
            ->get();
        return $client;
    }
}
