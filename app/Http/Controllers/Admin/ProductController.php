<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductsPacksSizes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    public function index()
    {
        $product = Product::with('subCategory', 'brand', 'productsPacksSizes')->get();
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
        try {

            $packs = $request->input('packs');

            $validatedData = $request->validate([
                'sub_category_id' => 'integer|exists:sub_categories,id',
                'brand_id' => 'integer|exists:brands,id',
                'product_name' => 'required|string',
                'product_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'description' => 'required|string',
                'product_size' => 'required|string',
            ]);

            // Handle image upload
            if ($request->hasFile('product_image')) {
                $imagePath = $request->file('product_image')->store('products', 'public');
                $validatedData['product_image'] = URL::to(Storage::url($imagePath));
            }

            
            $product = Product::create($validatedData);
            
            // Validate and process each pack
            if ($packs) {
                foreach ($packs as $pack) {
                    $pack['product_id'] = $product->id;
                    $packData = validator($pack, [
                        'product_id' => 'required|integer|exists:products,id',
                        'product_pack_id' => 'required|integer|exists:products_packs,id',
                        'pack_size' => 'required|string',
                        'pack_name' => 'required|string',
                        'pack_price' => 'required|numeric',
                        'pack_price_discount_percentage' => 'nullable|numeric',
                        'pack_price_discount' => 'nullable|numeric',
                        ])->validate();
                        if ($packData['pack_price_discount_percentage']) {
                            $packData['pack_price_discount'] = $packData['pack_price'] - (
                                ($packData['pack_price_discount_percentage'] / 100)
                                * $packData['pack_price']);
                        }
                        $pack = ProductsPacksSizes::create($packData);
                    }
             }

            return response()->json(['message' => 'Product Created Successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $packs = $request->input('packs');

            $validatedData = $request->validate([
                'sub_category_id' => 'sometimes|integer|exists:sub_categories,id',
                'brand_id' => 'sometimes|integer|exists:brands,id',
                'product_name' => 'sometimes|required|string',
                'product_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'sometimes|required|string',
                'product_size' => 'sometimes|required|string',
                'quantity' => 'sometimes|required|integer',
            ]);

            $product = Product::findOrFail($id);

            // Handle image upload
            if ($request->hasFile('product_image')) {
                // Delete old image if exists
                if ($product->product_image) {
                    $imagePath = $product->product_image;
                    $cleanPath = Str::replaceFirst('/storage', '', parse_url($imagePath, PHP_URL_PATH));
                    Storage::disk('public')->delete($cleanPath);
                }

                $imagePath = $request->file('product_image')->store('products', 'public');
                $validatedData['product_image'] = URL::to(Storage::url($imagePath));
            }

            $product->update($validatedData);

            // Validate and process each pack
            if ($packs) {
                // Delete existing packs for the product
                ProductsPacksSizes::where('product_id', $product->id)->delete();

                foreach ($packs as $pack) {
                    $pack['product_id'] = $product->id;
                    $packData = validator($pack, [
                        'product_id' => 'sometimes|integer|exists:products,id',
                        'product_pack_id' => 'sometimes|integer|exists:products_packs,id',
                        'pack_size' => 'sometimes|string',
                        'pack_name' => 'sometimes|string',
                        'pack_price' => 'sometimes|numeric',
                        'quantity' => 'sometimes|numeric',
                        'pack_price_discount_percentage' => 'sometimes|numeric',
                        'pack_price_discount' => 'sometimes|numeric',
                    ])->validate();

                    if ($packData['pack_price_discount_percentage']) {
                        $packData['pack_price_discount'] = $packData['pack_price'] - (
                            ($packData['pack_price_discount_percentage'] / 100)
                            * $packData['pack_price']);
                    }

                    ProductsPacksSizes::create($packData);
                }
            }

            return response()->json(['message' => 'Product Updated Successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            if ($product->product_image) {
                // dd($product->product_image);
                $imagePath = $product->product_image;
                // Remove domain and '/storage' prefix
                $cleanPath = Str::replaceFirst('/storage', '', parse_url($imagePath, PHP_URL_PATH));
                // Delete the image from the public disk
                Storage::disk('public')->delete($cleanPath);
                }
            $product->delete();
            return response()->json(["message" => "Deleted Successfully"]);
        } catch (\Exception $error) {
            return response()->json(["message" => $error->getMessage()], $error->getCode());
        }
    }
}
