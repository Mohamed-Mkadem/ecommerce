<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Wrapper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use RuntimeException;

class CatalogSeeder extends Seeder
{
    /**
     * Seed the reusable Sweetia demo catalog.
     */
    public function run(): void
    {
        $catalogPath = __DIR__.'/catalog.json';

        if (! is_file($catalogPath)) {
            throw new RuntimeException('The catalog seed data file is missing.');
        }

        $catalog = json_decode(file_get_contents($catalogPath), true, 512, JSON_THROW_ON_ERROR);

        foreach ($catalog as $wrapperData) {
            foreach ($wrapperData['media'] ?? [] as $mediaData) {
                $assetPath = __DIR__.'/assets/catalog/'.$mediaData['file_name'];

                if (! is_file($assetPath)) {
                    throw new RuntimeException("Catalog image [{$mediaData['file_name']}] is missing.");
                }
            }
        }

        foreach ($catalog as $wrapperData) {
            $wrapper = Wrapper::firstOrNew(['slug' => $wrapperData['slug']]);
            $wrapper->is_active = $wrapperData['is_active'];
            $wrapper->seo_title = $wrapperData['seo_title'] ?? null;
            $wrapper->seo_description = $wrapperData['seo_description'] ?? null;

            foreach ($wrapperData['translations'] as $translationData) {
                $translation = $wrapper->translateOrNew($translationData['locale']);
                $translation->title = $translationData['title'];
                $translation->description = $translationData['description'] ?? null;
            }

            $wrapper->save();

            $productsToSync = [];

            foreach ($wrapperData['products'] as $productData) {
                $englishTranslation = collect($productData['translations'])
                    ->firstWhere('locale', 'en');
                $englishName = $englishTranslation['name'] ?? null;

                $product = Product::query()
                    ->where('shipping_name', $productData['shipping_name'])
                    ->whereHas('translations', fn ($query) => $query
                        ->where('locale', 'en')
                        ->where('name', $englishName))
                    ->first() ?? new Product();

                $product->price = $productData['price'];
                $product->shipping_name = $productData['shipping_name'];
                $product->discount = $productData['discount'];
                $product->discount_type = $productData['discount_type'];

                foreach ($productData['translations'] as $translationData) {
                    $translation = $product->translateOrNew($translationData['locale']);
                    $translation->name = $translationData['name'];
                }

                $product->save();

                $productsToSync[$product->id] = Arr::only($productData['pivot'], [
                    'display_order',
                    'is_default',
                    'free_shipping',
                    'update_quantity',
                ]);
            }

            $wrapper->products()->sync($productsToSync);

            foreach ($wrapperData['media'] ?? [] as $mediaData) {
                if ($wrapper->getMedia($mediaData['collection_name'])->isNotEmpty()) {
                    continue;
                }

                $wrapper
                    ->addMedia(__DIR__.'/assets/catalog/'.$mediaData['file_name'])
                    ->preservingOriginal()
                    ->usingName($mediaData['name'])
                    ->usingFileName($mediaData['file_name'])
                    ->toMediaCollection($mediaData['collection_name'], 'public');
            }
        }
    }
}
