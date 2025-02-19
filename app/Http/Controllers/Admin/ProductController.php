<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ProductController extends Controller
{

    public function index()
    {
        $product = Product::all();
        return response()->json($product, 200);
    }

    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            return response()->json($product, 200);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }

    public function store(Request $request)
    {
        // dd($request->all);
        try {
            $validatedData = $request->validate([
                'category_id' => 'integer|exists:categories,id',
                'product_name' => 'required|string',
                // 'product_code' => 'required|string',
                'product_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
                'product_size' => 'required|integer',
                'product_pack_quantity' => 'required|integer',
                'product_price' => 'required|integer',
                'offer_percentage' => 'required|integer',
                'quantity' => 'required|integer',
            ]);

            // Handle image upload
            if ($request->hasFile('product_image')) {
                $imagePath = $request->file('product_image')->store('products', 'public');
                $validatedData['product_image'] = URL::to(Storage::url($imagePath));
            }

            $product = Product::create($validatedData);

            return response()->json(['message' => 'Product Created Successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        try {
            $validatedData = $request->validate([
                'category_id' => 'integer|exists:categories,id',
                'product_name' => 'required|string',
                'product_code' => 'required|string',
                'product_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
                'product_size' => 'required|integer',
                'product_pack_quantity' => 'required|integer',
                'product_price' => 'required|integer',
                'offer_percentage' => 'required|integer',
                'quantity' => 'required|integer',
            ]);

            // Find the product
            $product = Product::findOrFail($id);

            // Handle image upload if present
            if ($request->hasFile('product_image')) {
                // Delete the old image if it exists
                if ($product->product_image) {
                    Storage::disk('public')->delete($product->product_cover);
                }

                // Upload new image and update the path
                $imagePath = $request->file('product_cover')->store('products', 'public');
                $validatedData['product_cover'] = URL::to(Storage::url($imagePath));
            }

            // Update product
            $product->update($validatedData);

            return response()->json(['message' => 'Product Updated Successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return response()->json(["message" => "Deleted Successfully"]);
        } catch (\Exception $error) {
            return response()->json(["message" => $error->getMessage()], $error->getCode());
        }
    }
}
