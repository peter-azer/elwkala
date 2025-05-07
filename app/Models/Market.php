<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    /** @use HasFactory<\Database\Factories\MarketFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'area_id',
        'owner_name',
        'manager_name',
        'market_name',
        'phone2',
        'phone3',
        'address',
        'max_order_quantity',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    public function assign()
    {
        return $this->belongsTo(AssignedOrders::class);
    }
    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
