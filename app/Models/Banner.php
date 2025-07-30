<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    /** @use HasFactory<\Database\Factories\BannerFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_url',
        'title',
        'description',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
