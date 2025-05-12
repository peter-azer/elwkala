<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsPacksSizes extends Model
{
    protected $table = 'products_packs_sizes';

    protected $fillable = [
        'product_id',
        'product_pack_id',
        'pack_size',
        'pack_name',
        'pack_price',
        'pack_price_discount_percentage',
        'pack_price_discount',
    ];

    public function productsPacks()
    {
        return $this->belongsTo(ProductsPacks::class, 'product_pack_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function order()
    {
        return $this->hasMany(Order::class, 'products_packs_sizes_id');
    }
    public function cart()
    {
        return $this->hasMany(Cart::class, 'products_packs_sizes_id');
    }
}
