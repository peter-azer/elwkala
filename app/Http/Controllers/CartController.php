<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Market;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $market = Market::query()
            ->where('user_id', $user->id)
            ->first();
        $carts = Cart::query()
            ->where('market_id', $market->id)
            ->with(['product', 'productsPacksSizes'])
            ->get();

        // dd($carts);
        return response()->json($carts);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartRequest $request)
    {
        try {
            $validatedCart = $request->validate([
                'market_id' => 'required|exists:markets,id',
                'product_id' => 'required|exists:products,id',
                'products_packs_sizes_id' => 'required|integer|exists:products_packs_sizes,id',
                'quantity' => 'required|numeric|gt:0'
            ]);

            $cart = Cart::create($validatedCart);
            return response()->json(['message' => 'added to cart']);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartRequest $request, Cart $cart)
    {
        try {
            $validatedCart = $request->validate([
            'market_id' => 'nullable|exists:markets,id',
            'product_id' => 'nullable|exists:products,id',
            'products_packs_sizes_id' => 'required|integer|exists:products_packs_sizes,id',
            'quantity' => 'required|numeric|gt:0'
            ]);

            $cart->update($validatedCart);
            return response()->json(['message' => 'cart updated']);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();
        return response()->json(['message'=>'removed successfully']);
    }
}
