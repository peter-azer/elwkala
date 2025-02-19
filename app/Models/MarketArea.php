<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketArea extends Model
{
        /** @use HasFactory<\Database\Factories\MarketFactory> */
        use HasFactory;

        protected $fillable = [
            'market_id',
            'area_id'
        ];

        public function market(){
            return $this->belongsTo(Market::class);
        }

        public function area(){
            return $this->belongsTo(Area::class);
        }
}
