<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWrapperRequest;
use App\Http\Requests\UpdateWrapperRequest;
use App\Http\Resources\AdminWrapperResource;
use App\Models\Product;
use App\Models\Wrapper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class WrapperController extends Controller
{
    public function index(): Response
    {
        $wrappers = Wrapper::withCount('products')
            ->with('products')
            ->latest()
            ->get()
            ->map(fn(Wrapper $wrapper) => (new AdminWrapperResource($wrapper))->resolve());

        return Inertia::render('Admin/Wrappers/Index', ['wrappers' => $wrappers]);
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

        $wrapper = Wrapper::create([
            'caption' => $validated['caption'] ?? null,
            'title' => $validated['title'],
            'slug' => $this->uniqueSlug($validated['title']),
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

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
        $wrapper->load('products');

        return Inertia::render('Admin/Wrappers/Edit', [
            'wrapper' => $this->formatWrapperForForm($wrapper),
            'products' => $this->availableProducts(),
        ]);
    }

    public function update(UpdateWrapperRequest $request, Wrapper $wrapper): RedirectResponse
    {
        $validated = $request->validated();

        $slug = $wrapper->slug;
        if ($wrapper->title !== $validated['title']) {
            $slug = $this->uniqueSlug($validated['title'], $wrapper->id);
        }

        $wrapper->update([
            'caption' => $validated['caption'] ?? null,
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

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

    private function availableProducts(): array
    {
        return Product::query()
            ->orderBy('type')
            ->orderBy('id')
            ->get()
            ->map(fn(Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'type' => $product->type,
                'price' => $product->getFormattedPrice(),
                'status' => $product->status,
                'main_image_url' => $product->getFirstMediaUrl('images')
                    ?: asset('storage/products/default.png'),
            ])
            ->all();
    }

    private function formatWrapperForForm(Wrapper $wrapper): array
    {
        return [
            'id' => $wrapper->id,
            'slug' => $wrapper->slug,
            'caption' => $wrapper->caption,
            'title' => $wrapper->title,
            'description' => $wrapper->description,
            'is_active' => $wrapper->is_active,
            'products' => $wrapper->products
                ->sortBy(fn(Product $product) => $product->pivot->display_order)
                ->values()
                ->map(fn(Product $product) => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'type' => $product->type,
                    'price' => $product->getFormattedPrice(),
                    'main_image_url' => $product->getFirstMediaUrl('images')
                        ?: asset('storage/products/default.png'),
                    'display_order' => $product->pivot->display_order,
                    'is_default' => (bool) $product->pivot->is_default,
                    'free_shipping' => (bool) $product->pivot->free_shipping,
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
