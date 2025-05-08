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
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            $product->product_code = self::generateProductId();
        });
    }

    public static function generateProductId()
    {
        $latest = self::latest()->first();
        $number = $latest ? intval(substr($latest->product_code, 5)) + 1 : 1;
        return 'PROD-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
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
