<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Market;
use App\Services\OrderIdService;
use App\Models\Product;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;

class OrderController extends Controller
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
        $orders = Order::query()
            ->where('market_id', $market->id)
            ->with('product')
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
                    'quantity' => 'required|integer|min:1',
                    'paid' => 'nullable|boolean',
                    'handed' => 'nullable|boolean',
                ])->validate();

                // Fetch product price from database
                $product = Product::findOrFail($validatedData['product_id']);
                if($product->offer_percentage_price == 0 || $product->offer_percentage_price == null){
                    $itemPrice = $product->product_price * $validatedData['quantity'];
                }else{
                    $itemPrice = $product->offer_percentage_price * $validatedData['quantity'];
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
