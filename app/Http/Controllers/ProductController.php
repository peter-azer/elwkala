<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $market_id = $user->market()->first()->id;

        // Get the user's cart items (not ordered)
        $cartItems = Cart::where('market_id', $market_id)
            ->where('ordered', false)
            ->get()
            ->keyBy('products_packs_sizes_id');

        // Get all products with relations
        $products = Product::with(['subCategory', 'brand', 'productsPacksSizes', 'productsPacksSizes.productsPacks'])->get();

        // Attach cart quantity to each products_packs_size if it exists in the cart
        $products = $products->map(function ($product) use ($cartItems) {
            if ($product->productsPacksSizes) {
                foreach ($product->productsPacksSizes as $pps) {
                    $pps->cart_quantity = $cartItems->has($pps->id) ? $cartItems[$pps->id]->quantity : 0;
                }
            }
            return $product;
        });

        return response()->json($products, 200);
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
