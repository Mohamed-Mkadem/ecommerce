<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Invoiceable;
use Illuminate\Http\Request;
use App\Http\Resources\InvoiceResource;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        if ($request->filled('type') && $request->type != 'all') {
            $query->where('type',  $request->type);
        }
        if ($request->filled('category') && $request->category != 'all') {
            $query->where('category',  $request->category);
        }

        if ($request->filled('minAmount')) {
            $query->where('amount', '>=', ($request->minAmount * 1000));
        }
        if ($request->filled('maxAmount')) {
            $query->where('amount', '<=', ($request->maxAmount * 1000));
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



                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;

                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }


        $invoices = $query->paginate(20)->withQueryString();
        return Inertia::render('Admin/Invoices/Index', ['invoices' => InvoiceResource::collection($invoices)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Invoices/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        $validated = $request->validated();
        $invoiceable = Invoiceable::create();
        Invoice::create([
            'title' => $validated['title'],
            'amount' => ($validated['amount'] * 1000),
            'category' => $validated['category'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'invoiceable_type' => get_class($invoiceable),
            'invoiceable_id' => $invoiceable->id,

        ]);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return Inertia::render('Admin/Invoices/Show', ['invoice' => new InvoiceResource($invoice)]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        return Inertia::render('Admin/Invoices/Edit', ['invoice' => $invoice]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $validated = $request->validated();
        $validated['amount'] = ($validated['amount'] * 1000);
        $invoice->update($validated);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {

        try {

            if ($invoice->invoiceable instanceof Invoiceable) {
                $invoice->delete();
                return redirect()->back();
            } else {
                throw new \Exception();
            }
        } catch (\Throwable $th) {
            abort(500);
        }
    }
}
