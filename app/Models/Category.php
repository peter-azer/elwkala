<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'category_name',
        'category_cover',
        'description',
    ];

    public function product(){
        return $this->hasMany(Product::class);
    }
    public function brands(){
        return $this->hasMany(Brand::class);
    }
}
