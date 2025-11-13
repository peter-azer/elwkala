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
        'hide',
    ];

    public function subCategory(){
        return $this->hasMany(SubCategory::class);
    }
    public function brands(){
        return $this->hasMany(Brand::class);
    }
}
