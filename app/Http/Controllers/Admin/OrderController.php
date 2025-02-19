<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderIdService;
use App\Models\Product;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $orders = Order::query()
                ->with('market', 'product')
                ->get();
            return response()->json($orders);
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
                    'paid' => 'required|boolean',
                    'handed' => 'required|boolean',
                ])->validate();

                // Fetch product price from database
                $product = Product::findOrFail($validatedData['product_id']);
                $itemPrice = $product->product_price * $validatedData['quantity'];

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
    public function show(string $id)
    {
        try {
            $order = Order::findOrFail($id);
            return response()->json($order, 200);
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()], $error->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Find the order or fail
            $order = Order::findOrFail($id);
    
            // Validate the incoming request
            $validatedData = $request->validate([
                'market_id' => 'required|integer|exists:markets,id',
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'paid' => 'required|boolean',
                'handed' => 'required|boolean',
            ]);
    
            // Fetch the product to calculate the total price
            $product = Product::findOrFail($validatedData['product_id']);
            $validatedData['total_order_price'] = $product->product_price * $validatedData['quantity'];
    
            // Update the order
            $order->update($validatedData);
    
            return response()->json([
                'message' => 'Order Updated Successfully',
                'order' => $order
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the order or fail with 404
            $order = Order::findOrFail($id);
    
            // Delete the order
            $order->delete();
    
            return response()->json([
                'message' => 'Order Deleted Successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Cannot delete order due to related records. Ensure dependencies are removed first.'
            ], 409); // Conflict status code
        }
    }
    
}
