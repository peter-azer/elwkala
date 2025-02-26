<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    /** @use HasFactory<\Database\Factories\AreaFactory> */
    use HasFactory;
    protected $fillable = [
        'area'
    ];

    public function market(){
        return $this->hasMany(Market::class);
    }
    public function area(){
        return $this->hasMany(UserArea::class);
    }
}
