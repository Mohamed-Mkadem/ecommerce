<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ProductResource;
use Spatie\Activitylog\Models\Activity;
use App\Http\Resources\ActivityResource;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $query = Product::query();


        if ($request->filled('search')) {
            $query->whereTranslationLike('name', '%' . $request->search . '%', app()->getLocale());
        }


        if ($request->filled('minPrice')) {
            $query->where('price', '>=', ($request->minPrice * 1000));
        }
        if ($request->filled('maxPrice')) {
            $query->where('price', '<=', ($request->maxPrice * 1000));
        }


        if ($request->filled('minRate')) {
            $query->where('rate', '>=', $request->minRate);
        }
        if ($request->filled('maxRate')) {
            $query->where('rate', '<=', $request->maxRate);
        }

        // Add a subquery to count the delivered orders where the products appeared
        $query->addSelect(['delivered_orders_count' => function ($query) {
            $query->selectRaw('SUM(order_product.quantity)')
                ->from('order_product')
                ->join('orders', 'order_product.order_id', '=', 'orders.id')
                ->whereColumn('order_product.product_id', 'products.id')
                ->where('orders.status', 'delivered');
        }]);

        // Apply the minOrdersCount filter
        if ($request->filled('minOrdersCount')) {
            $query->havingRaw('COALESCE(delivered_orders_count, 0) >= ?', [(int)$request->minOrdersCount]);
        }

        // Apply the maxOrdersCount filter
        if ($request->filled('maxOrdersCount')) {
            $query->havingRaw('COALESCE(delivered_orders_count, 0) <= ?', [(int)$request->maxOrdersCount]);
        }





        if ($request->filled('sort')) {
            switch ($request->sort) {

                case 'lowest_price':
                    $query->orderBy('price', 'asc');
                    break;
                case 'highest_rate':
                    $query->orderBy('rate', 'desc');
                    break;
                case 'lowest_rate':
                    $query->orderBy('rate', 'asc');
                    break;
                case 'highest_orders':
                    $query->orderBy('delivered_orders_count', 'desc');
                    break;
                case 'lowest_orders':
                    $query->orderBy('delivered_orders_count', 'asc');

                    break;
                default:
                    $query->orderBy('price', 'desc');
                    break;
            }
        } else {
            $query->orderBy('price', 'desc');
        }


        $products = $query->paginate()->withQueryString();
        return Inertia::render('Admin/Products/Index', [
            'products' => ProductResource::collection($products),
            'filters' => $request->all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return Inertia::render('Admin/Products/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {


        $validated = $request->validated();
        $validated['price'] = $validated['price'] * 1000;

        $product = new Product();
        $product->fill($validated);
        $product->save();

        return redirect()->back()->with('success', 'Product added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {

        $activities = Activity::where('subject_type', Product::class)
            ->where('subject_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return Inertia::render('Admin/Products/Show', [
            'product' =>
            new  ProductResource($product),
            'activities' => ActivityResource::collection($activities),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        $validated['price'] = $validated['price'] * 1000;

        $product->fill($validated);
        $product->save();

        return redirect()->back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();

        try {


            if ($product->orders()->exists()) {
                $product->delete();
            } else {
                $product->deleteTranslations();
                $product->forceDelete();
            }

            DB::commit();



            return redirect()->route('products.index');
        } catch (\Exception $e) {

            DB::rollBack();




            return redirect()->back()->with('error', 'Failed to delete the product.');
        }
    }
}
