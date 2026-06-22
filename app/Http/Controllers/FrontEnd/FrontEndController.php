<?php

namespace App\Http\Controllers\FrontEnd;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\State;
use App\Models\Wrapper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\FrontEndProductResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\WrapperListingResource;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;

class FrontEndController extends Controller
{
    public function home()
    {
        $wrappers = Wrapper::query()
            ->where('is_active', true)
            ->whereHas('products')
            ->with('products')
            ->take(12)
            ->get()
            ->map(fn(Wrapper $wrapper) => (new WrapperListingResource($wrapper))->resolve());

        return Inertia::render('FrontEnd/Home', [
            'wrappers' => $wrappers,
        ]);
    }

    public function about()
    {
        return Inertia::render('FrontEnd/About');
    }
    public function contact()
    {
        return Inertia::render('FrontEnd/Contact');
    }
    public function terms()
    {
        return Inertia::render('FrontEnd/Terms');
    }
    public function privacy()
    {
        return Inertia::render('FrontEnd/Privacy');
    }
    public function cart()
    {
        return Inertia::render('FrontEnd/Cart');
    }
    public function checkout()
    {
        $states = State::all();

        return Inertia::render('FrontEnd/Checkout', [
            'states' => StateResource::collection($states)
        ]);
    }

    public function shop(Request $request)
    {
        $query = Wrapper::query()
            ->where('is_active', true)
            ->whereHas('products')
            ->with('products');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $locale = app()->getLocale();

            $query->where(function (Builder $q) use ($search, $locale) {
                $q->where('title', 'like', $search)
                    ->orWhereHas('products', function (Builder $pq) use ($search, $locale) {
                        $pq->where('status', 'published')
                            ->whereTranslationLike('name', $search, $locale);
                    });
            });
        }

        $sort = $request->input('sort', 'lowest_price');
        $defaultPrice = $this->defaultVariantSubquery('price');
        $defaultRate = $this->defaultVariantSubquery('rate');

        match ($sort) {
            'highest_price' => $query->orderBy($defaultPrice, 'desc'),
            'highest_rate' => $query->orderBy($defaultRate, 'desc'),
            'lowest_rate' => $query->orderBy($defaultRate, 'asc'),
            default => $query->orderBy($defaultPrice, 'asc'),
        };

        $wrappers = $query->paginate()
            ->withQueryString()
            ->through(fn(Wrapper $wrapper) => (new WrapperListingResource($wrapper))->resolve());

        return Inertia::render('FrontEnd/Shop', [
            'wrappers' => $wrappers,
            'filters' => $request->all(),
        ]);
    }

    public function wrapper(Request $request, Wrapper $wrapper)
    {
        if (! $wrapper->is_active) {
            abort(404);
        }

        $wrapper->load('products');

        if ($wrapper->products->isEmpty()) {
            abort(404);
        }

        $default = $wrapper->defaultProduct();
        $selectedProductId = (int) $request->input('variant', $default->id);

        if (! $wrapper->products->contains('id', $selectedProductId)) {
            $selectedProductId = $default->id;
        }

        $variants = $wrapper->products->map(function (Product $product) use ($wrapper) {
            return array_merge(
                (new FrontEndProductResource($product))->resolve(),
                [

                    'is_default' => (bool) $product->pivot->is_default,
                    'free_shipping' => (bool) $product->pivot->free_shipping,
                    'update_quantity' => $product->pivot->update_quantity ?? 1,
                    'wrapper_main_image_url' => $wrapper->getFirstMediaUrl('images') ?: asset('storage/products/product.webp'),
                    'wrapper_title' => $wrapper->title,
                ]
            );
        });

        return Inertia::render('FrontEnd/Wrapper', [
            'wrapper' => [
                'id' => $wrapper->id,
                'slug' => $wrapper->slug,
                'title' => $wrapper->title,
                'caption' => $wrapper->caption,
                'description' => $wrapper->description,
                'main_image_url' => $wrapper->getFirstMediaUrl('images') ?: asset('storage/products/product.webp'),
                'media' => $wrapper->getMedia('images')->map(fn($media) => [
                    'id' => $media->id,
                    'original_url' => $media->getUrl(),
                    'file_name' => $media->file_name,
                ])->toArray(),
            ],
            'variants' => $variants,
            'selected_product_id' => $selectedProductId,
            'states' => StateResource::collection(State::all()),
            'reviews' => ReviewResource::collection(
                Review::where('product_id', $selectedProductId)->paginate()
            ),
        ]);
    }

    private function defaultVariantSubquery(string $column): Builder
    {
        return Product::query()
            ->select("products.{$column}")
            ->join('product_wrapper', 'products.id', '=', 'product_wrapper.product_id')
            ->whereColumn('product_wrapper.wrapper_id', 'wrappers.id')
            ->orderByDesc('product_wrapper.is_default')
            ->orderBy('product_wrapper.display_order')
            ->limit(1);
    }
    public function packs(Request $request)
    {
        $query = Product::query();
        $query->where('type', 'pack');
        $query->where('status', 'published');

        if ($request->filled('search')) {
            $query->whereTranslationLike('name', '%' . $request->search . '%', app()->getLocale());
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
                case 'highest_price':
                    $query->orderBy('price', 'desc');
                    break;
            }
        } else {
            $query->orderBy('price', 'asc');
        }


        $products = $query->paginate()->withQueryString()
            ->through(fn($product) => [
                'id' => $product->id,
                'rate' => $product->rate && $product->rate != 0 ? $product->rate : null,
                'name' => $product->name,
                'type' => $product->type,
                'price' => $product->getFormattedPrice(),
                'main_image_url' => asset('storage/products/default.png'),
                'translations' => $product->translations,
                'discount' => $product->discount,
                'discount_type' => $product->discount_type,

            ]);

        return Inertia::render('FrontEnd/Packs', [
            'products' => $products,
            'filters' => $request->all()
        ]);
    }

    public function product(Product $product)
    {
        $product->status != 'published' ? abort(404) : '';
        $reviews = Review::where('product_id', $product->id)->paginate();
        return Inertia::render('FrontEnd/Product', [
            'product' => new ProductResource($product),
            'reviews' => ReviewResource::collection($reviews),
        ]);
    }
}
