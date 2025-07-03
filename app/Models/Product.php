<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'sub_category_id',
        'brand_id',
        'product_name',
        'product_code',
        'product_image',
        'description',
        'product_size',
        'quantity',
        'hide',
    ];

    public function cart()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function recommended(){
        return $this->hasMany(RecomendedProducts::class, 'product_id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function productsPacksSizes()
    {
        return $this->hasMany(ProductsPacksSizes::class, 'product_id');
    }
}
