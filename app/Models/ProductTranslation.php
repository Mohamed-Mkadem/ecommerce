<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'description'];

    
    protected static function booted()
    {
        static::updated(function ($translation) {
            // Get the related product
            $product = $translation->product;

            if ($product) {
                activity()
                    ->performedOn($product)
                    ->causedBy(auth()->user() ?? null)
                    ->withProperties([
                        'locale' => $translation->locale,
                        'changed' => $translation->getChanges(),
                    ])
                    ->log('product.updated');
            }
        });
    }

    // Add this relationship if not already present
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
