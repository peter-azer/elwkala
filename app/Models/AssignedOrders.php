<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedOrders extends Model
{
    
    protected $fillable = [
        'user_id',
        'market_id',
        'order_id',
    ];

    public function users(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function markets(){
        return $this->belongsTo(Market::class, 'market_id');
    }
    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }
}
