<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsPacks extends Model
{
    protected $table = 'products_packs';

    protected $fillable = [
        'pack_name',
        'pack_size',
    ];

    public function productsPacksSizes()
    {
        return $this->hasMany(ProductsPacksSizes::class, 'product_pack_id');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_packs_sizes', 'product_pack_id', 'product_id');
    }
}
