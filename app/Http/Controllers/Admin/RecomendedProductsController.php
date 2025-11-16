<?php

namespace App\Http\Controllers\Admin;

use App\Models\RecomendedProducts;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecomendedProductsRequest;
use App\Http\Requests\UpdateRecomendedProductsRequest;
use App\Models\Product;

class RecomendedProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $recommended = Product::where('sub_category_id', $id)->inRandomOrder()->take(9)->get();
        return response()->json($recommended, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecomendedProductsRequest $request)
    {
        try {
            // Validate that product_ids is an array and all exist in products table
            $validatedData = $request->validate([
                'product_ids' => 'required|array',
                'product_ids.*' => 'integer|exists:products,id',
            ]);

            // Delete all previous recommended products
            RecomendedProducts::truncate();

            // Prepare data for bulk insert
            $data = collect($validatedData['product_ids'])->map(function ($id) {
                return ['product_id' => $id];
            })->toArray();

            // Insert all new recommended products
            RecomendedProducts::insert($data);

            return response()->json([
                'message' => 'Recommended products updated successfully.',
                'data' => $data,
            ], 201);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $recommended = RecomendedProducts::findOrFail($id);
            $recommended->delete();
            return response()->json(['message' => 'Recommended product deleted successfully'], 200);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }
}
