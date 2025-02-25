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
        return $this->hasMany(User::class);
    }
    public function markets(){
        return $this->hasMany(Market::class);
    }
}
