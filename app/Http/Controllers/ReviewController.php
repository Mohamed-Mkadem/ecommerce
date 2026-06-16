<?php

namespace App\Http\Controllers;

use App\Events\ReviewCreated;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ReviewResource;
use App\Http\Requests\StoreReviewRequest;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Review::query();

        if ($request->filled('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->whereTranslationLike('name', '%' . $request->search . '%', app()->getLocale());
            });
        }
        if ($request->filled('stars')) {
            $query->whereIn('stars', $request->stars);
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


        $reviews = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Reviews/Index', ['reviews' => ReviewResource::collection($reviews), 'filters' => $request->all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product)
    {
        return Inertia::render('FrontEnd/CreateReview', ['product' => $product]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewRequest $request)
    {
        $validated = $request->validated();
        $order = Order::find($validated['orderID']);
        $product = Product::find($validated['productID']);

        $review = Review::create([
            'client_name' => $validated['name'],
            'product_id' => $validated['productID'],
            'stars' => $validated['stars'],
            'comment' => $validated['comment'] ?? null,
            'client_id' => $order->client_id,
        ]);
        $product->updateRate();
        event(new ReviewCreated($review));
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return Inertia::render('Admin/Reviews/Show', ['review' => new ReviewResource($review)]);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $product = $review->product;
        $review->delete();
        $product->updateRate();

        return redirect()->back();
    }
}
