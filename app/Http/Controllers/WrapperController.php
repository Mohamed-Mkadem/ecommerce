<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWrapperRequest;
use App\Http\Requests\UpdateWrapperRequest;
use App\Http\Resources\AdminWrapperResource;
use App\Models\Product;
use App\Models\Wrapper;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class WrapperController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Wrapper::withCount('products')
            ->with('products')
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->whereTranslationLike(
                    'title',
                    '%' . $request->input('name') . '%',
                    app()->getLocale(),
                );
            })
            ->when(in_array($request->input('status'), ['active', 'inactive'], true), function ($query) use ($request) {
                $query->where('is_active', $request->input('status') === 'active');
            })
            ->when($request->filled('minProductsCount'), function ($query) use ($request) {
                $query->has('products', '>=', max(0, (int) $request->input('minProductsCount')));
            })
            ->when($request->filled('maxProductsCount'), function ($query) use ($request) {
                $query->has('products', '<=', max(0, (int) $request->input('maxProductsCount')));
            });

        match ($request->input('sort')) {
            'highest_products' => $query->orderByDesc('products_count'),
            'lowest_products' => $query->orderBy('products_count'),
            default => $query->latest(),
        };

        $wrappers = AdminWrapperResource::collection(
            $query->paginate(2)->withQueryString()
        );


        return Inertia::render('Admin/Wrappers/Index', [
            'wrappers' => $wrappers,
            'filters' => $request->only([
                'name',
                'sort',
                'status',
                'minProductsCount',
                'maxProductsCount',
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Wrappers/Create', [
            'products' => $this->availableProducts(),
        ]);
    }

    public function store(StoreWrapperRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $translations = [
            'en' => [
                'title' => $validated['en']['title'],
                'description' => $validated['en']['description'] ?? null,
            ],
            'fr' => [
                'title' => $validated['fr']['title'],
                'description' => $validated['fr']['description'] ?? null,
            ],
            'ar' => [
                'title' => $validated['ar']['title'],
                'description' => $validated['ar']['description'] ?? null,
            ],
        ];

        $wrapper = Wrapper::create([
            'slug' => $this->uniqueSlug($validated['en']['title']),
            'is_active' => $validated['is_active'],
            'en' => $translations['en'],
            'fr' => $translations['fr'],
            'ar' => $translations['ar'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $wrapper->addMedia($image)->toMediaCollection('images');
            }
        }

        $wrapper->products()->sync($this->syncPayload($validated['products']));

        return redirect()
            ->route('wrappers.create')
            ->with('success', __('Wrapper.created_successfully'));
    }

    public function show(Wrapper $wrapper): Response
    {
        $wrapper->load('products');

        return Inertia::render('Admin/Wrappers/Show', [
            'wrapper' => (new AdminWrapperResource($wrapper, detailed: true))->resolve(),
        ]);
    }

    public function edit(Wrapper $wrapper): Response
    {
        $wrapper->load('products.translations');

        return Inertia::render('Admin/Wrappers/Edit', [
            'wrapper' => $this->formatWrapperForForm($wrapper),
            'products' => $this->availableProducts($wrapper),
        ]);
    }

    public function update(UpdateWrapperRequest $request, Wrapper $wrapper): RedirectResponse
    {
        $validated = $request->validated();

        $wrapper->update([
            'is_active' => $validated['is_active'],
        ]);

        foreach (['en', 'fr', 'ar'] as $locale) {
            $wrapper->translateOrNew($locale)->title = $validated[$locale]['title'];
            $wrapper->translateOrNew($locale)->description = $validated[$locale]['description'] ?? null;
        }

        $wrapper->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $wrapper->addMedia($image)->toMediaCollection('images');
            }
        }

        if (!empty($validated['deleted_media'])) {
            foreach ($validated['deleted_media'] as $mediaId) {
                $wrapper->media()->where('id', $mediaId)->first()?->delete();
            }
        }

        $wrapper->products()->sync($this->syncPayload($validated['products']));

        return redirect()
            ->route('wrappers.show', $wrapper)
            ->with('success', __('Wrapper.updated_successfully'));
    }

    public function destroy(Wrapper $wrapper): RedirectResponse
    {
        $wrapper->delete();

        return redirect()
            ->route('wrappers.index')
            ->with('success', __('Wrapper.deleted_successfully'));
    }

    private function availableProducts(?Wrapper $wrapper = null): array
    {
        return Product::query()
            ->with('translations')
            ->whereDoesntHave('wrappers', function ($query) use ($wrapper) {
                if ($wrapper) {
                    $query->where('wrappers.id', '!=', $wrapper->getKey());
                }
            })
            ->orderBy('price', 'asc')
            ->orderBy('id')
            ->get()
            ->map(fn(Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'translations' => $product->translations
                    ->mapWithKeys(fn($translation) => [$translation->locale => $translation->name])
                    ->all(),
                'price' => $product->getFormattedPrice(),

            ])
            ->all();
    }

    private function formatWrapperForForm(Wrapper $wrapper): array
    {
        return [
            'id' => $wrapper->id,
            'title' => $wrapper->title,
            'slug' => $wrapper->slug,
            'en' => [
                'title' => $wrapper->translate('en')->title,
                'description' => $wrapper->translate('en')->description,
            ],
            'fr' => [
                'title' => $wrapper->translate('fr')->title,
                'description' => $wrapper->translate('fr')->description,
            ],
            'ar' => [
                'title' => $wrapper->translate('ar')->title,
                'description' => $wrapper->translate('ar')->description,
            ],
            'is_active' => $wrapper->is_active,
            'media' => $wrapper->getMedia('images')->map(fn($m) => [
                'id' => $m->id,
                'name' => $m->file_name,
                'size' => $m->size,
                'url' => $m->getUrl(),
            ])->all(),
            'products' => $wrapper->products
                ->sortBy(fn(Product $product) => $product->pivot->display_order)
                ->values()
                ->map(fn(Product $product) => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'translations' => $product->translations
                        ->mapWithKeys(fn($translation) => [$translation->locale => $translation->name])
                        ->all(),
                    'type' => $product->type,
                    'price' => $product->getFormattedPrice(),
                    'display_order' => $product->pivot->display_order,
                    'is_default' => (bool) $product->pivot->is_default,
                    'free_shipping' => (bool) $product->pivot->free_shipping,
                    'update_quantity' => $product->pivot->update_quantity ?? 1,
                ])
                ->all(),
        ];
    }

    private function syncPayload(array $products): array
    {
        return collect($products)
            ->mapWithKeys(fn(array $item) => [
                $item['product_id'] => [
                    'display_order' => $item['display_order'],
                    'is_default' => $item['is_default'],
                    'free_shipping' => $item['free_shipping'],
                    'update_quantity' => $item['update_quantity'],
                ],
            ])
            ->all();
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $counter = 1;

        while (
            Wrapper::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
