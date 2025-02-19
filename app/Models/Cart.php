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
        'quantity',
        'ordered'
    ];

    public function market(){
        return $this->hasMany(Market::class, 'market_id');
    }
    public function product(){
        return $this->hasMany(Product::class, 'product_id');
    }
}
