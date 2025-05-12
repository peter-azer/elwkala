<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    /** @use HasFactory<\Database\Factories\CartFactory> */
    use HasFactory;

    protected $fillable = [
        'market_id',
        'product_id',
        'products_packs_sizes_id',
        'quantity',
        'ordered'
    ];

    public function market()
    {
        return $this->hasMany(Market::class, 'market_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
        public function productsPacksSizes()
    {
        return $this->belongsTo(ProductsPacksSizes::class, 'products_packs_sizes_id');
    }
}
