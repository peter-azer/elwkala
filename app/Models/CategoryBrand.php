<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryBrand extends Model
{
    
    protected $fillable = [
        'sub_category_id',
        'brand_id',
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
