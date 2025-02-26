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
        return $this->belongsTo(Market::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
