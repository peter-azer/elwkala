<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'market_id',
        'product_id',
        'order_id',
        'quantity',
        'total_order_price',
        'paid',
        'handed'
    ];

    public function market()
    {
        return $this->hasMany(Market::class, 'market_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
