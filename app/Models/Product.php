<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'product_name',
        'product_code',
        'product_image',
        'description',
        'product_size',
        'product_pack_quantity',
        'product_price',
        'offer_percentage',
        'offer_percentage_price',
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
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function cart()
    {
        return $this->belongsTo(Category::class);
    }
}
