<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecomendedProducts extends Model
{
    /** @use HasFactory<\Database\Factories\RecomendedProductsFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}
