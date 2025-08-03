<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderIdService;
use App\Models\Product;
use App\Models\ProductsPacksSizes;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $orders = Order::query()
                ->with('market', 'product', 'product.productsPacksSizes')
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
                   'products_packs_sizes_id' => 'required|integer|exists:products_packs_sizes,id',
                   'quantity' => 'required|integer|min:1',
                   'paid' => 'required|boolean',
                   'handed' => 'required|boolean',
               ])->validate();

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
    public function show(string $id)
    {
        try {
            $order = Order::where('order_id', $id)->whith('market', 'product', 'productsPacksSizes')->get();
            return response()->json($order, 200);
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
    try {
        $cart = $request->input('cart');
        $totalOrderPrice = 0; // Initialize total order price

        foreach ($cart as $form) {
        $validatedData = validator($form, [
            'market_id' => 'required|integer|exists:markets,id',
            'product_id' => 'required|integer|exists:products,id',
            'products_packs_sizes_id' => 'required|integer|exists:products_packs_sizes,id',
            'quantity' => 'required|integer|min:1',
            'paid' => 'required|boolean',
            'handed' => 'required|boolean',
        ])->validate();

        // Fetch product price from database
        $product = ProductsPacksSizes::findOrFail($validatedData['products_packs_sizes_id']);
        $order = Order::where('id', $id)->where('products_packs_sizes_id', $validatedData['products_packs_sizes_id'])->first();

        if (!$order) {
            return response()->json(['error' => 'Order item not found'], 404);
        }

        // Adjust product quantity
        $quantityDifference = $validatedData['quantity'] - $order->quantity;
        if ($quantityDifference > 0 && $product->quantity < $quantityDifference) {
            return response()->json(['error' => 'Insufficient product quantity'], 422);
        }
        $product->quantity -= $quantityDifference;
        $product->save();

        // Calculate item price
        if ($product->pack_price_discount_percentage == 0 || $product->pack_price_discount_percentage == null) {
            $itemPrice = $product->pack_price * $validatedData['quantity'];
        } else {
            $discountedPrice = $product->pack_price * (1 - ($product->pack_price_discount_percentage / 100));
            $itemPrice = $discountedPrice * $validatedData['quantity'];
        }

        // Add current item price to total order price
        $totalOrderPrice += $itemPrice;

        // Update the order
        $order->update([
            'market_id' => $validatedData['market_id'],
            'product_id' => $validatedData['product_id'],
            'products_packs_sizes_id' => $validatedData['products_packs_sizes_id'],
            'quantity' => $validatedData['quantity'],
            'paid' => $validatedData['paid'],
            'handed' => $validatedData['handed'],
            'total_order_price' => $itemPrice,
        ]);
        }

        return response()->json([
        'message' => 'Order Updated Successfully',
        'order_id' => $id,
        'total_order_price' => $totalOrderPrice
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
