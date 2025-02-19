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
        'phone2',
        'phone3',
        'owner_name',
        'manager_name',
        'market_name',
        'address',
        'area_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function area(){
        return $this->belongsTo(MarketArea::class);
    }
}
