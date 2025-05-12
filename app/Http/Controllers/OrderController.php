<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Market;
use App\Services\OrderIdService;
use App\Models\Product;
use App\Models\ProductsPacksSizes;
use App\Models\Cart;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{

            $user = auth()->user();
            $market = Market::query()
            ->where('user_id', $user->id)
            ->first();
            $orders = Order::query()
            ->where('market_id', $market->id)
            ->with('product', 'product.productsPacksSizes')
            ->get()
            ->groupBy('order_id')
            ->map(function ($group) {
                $total = $group->sum('total_order_price');
                return [
                    'orders' => $group,
                    'total' => $total
                ];
            });
            
            return response()->json($orders);
        }catch(\Exception $error){
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $user = auth()->user();
        $market = Market::query()
            ->where('user_id', $user->id)
            ->first();
       // Generate a unique order_id
       $orderId = OrderIdService::generate();
       $cart = $request->input('cart');
       // dd($request->input('forms'));

       try {
           $totalOrderPrice = 0; // Initialize total order price

           foreach ($cart as $form) {
               $validatedData = validator($form, [
                   'market_id' => 'required|integer|exists:markets,id',
                   'product_id' => 'required|integer|exists:products,id',
                   'products_packs_sizes_id' => 'required|integer|exists:products_packs_sizes,id',
                   'quantity' => 'required|integer|min:1',
                   'paid' => 'sometimes|boolean',
                   'handed' => 'sometimes|boolean',
               ])->validate();
                // Make sure to update the cart item to mark it as ordered
               $cartItem = Cart::findOrFail($form['id']);
                $cartItem->update([
                    'ordered' => true,
                ]);
               // Fetch product price from database
               $product = ProductsPacksSizes::findOrFail($validatedData['products_packs_sizes_id']);
               // fetch product and decrease quantity
               if($validatedData['quantity'] > 0) {
                   if ($product->quantity < $validatedData['quantity']) {
                       return response()->json(['error' => 'Insufficient product quantity'], 422);
                   }else{
                       $product->quantity -= $validatedData['quantity'];
                       $product->save();
                    }
               }
               $itemPrice = $product->pack_price * $validatedData['quantity'];
               if($product->pack_price_discount_percentage == 0 || $product->pack_price_discount_percentage == null){
                   
                   $itemPrice = $product->pack_price * $validatedData['quantity'];
               }else{
                   $discountedPrice = $product->pack_price * (1 - ($product->pack_price_discount_percentage / 100));
                   $itemPrice = $discountedPrice * $validatedData['quantity'];
               }

               // Add current item price to total order price
               $totalOrderPrice += $itemPrice;

               // Add order_id and item price to the data
               $validatedData['order_id'] = $orderId;
               $validatedData['total_order_price'] = $itemPrice;

               // Create the order
               Order::create($validatedData);
           }

           return response()->json([
               'message' => 'Orders Submitted Successfully',
               'order_id' => $orderId,
               'total_order_price' => $totalOrderPrice
           ], 201);
       } catch (\Exception $e) {
           return response()->json(['error' => $e->getMessage()], 500);
       }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $details = Order::query()
            ->where('id', $order->id)
            ->with('product')
            ->first();
        return response()->json($details);
    }
}
