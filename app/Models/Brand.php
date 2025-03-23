<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    /** @use HasFactory<\Database\Factories\BrandFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_name',
        'brand_description',
        'brand_logo',
    ];


    public function products(){
        return $this->hasMany(Product::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
