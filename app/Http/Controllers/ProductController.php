<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('hide', false)
        ->with('subCategory', 'brand', 'productsPacksSizes', 'productsPacksSizes.productsPacks')
        ->get();
        return response()->json($products);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product = Product::where('id', $product->id)->with('subCategory', 'brand', 'productsPacksSizes', 'productsPacksSizes.productsPacks')->get();
        try {
            return response()->json($product);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }
}
