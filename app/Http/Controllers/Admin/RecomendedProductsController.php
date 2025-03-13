<?php

namespace App\Http\Controllers\Admin;

use App\Models\RecomendedProducts;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecomendedProductsRequest;
use App\Http\Requests\UpdateRecomendedProductsRequest;

class RecomendedProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recommended = RecomendedProducts::all();
        return response()->json($recommended, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecomendedProductsRequest $request)
    {
        try {
            $validatedData = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
            ]);
            $recommended = RecomendedProducts::create($validatedData);
            return response()->json($recommended, 201);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $recommended = RecomendedProducts::findOrFail($id);
            $recommended->delete();
            return response()->json(['message' => 'Recommended product deleted successfully'], 200);
        }catch(\Exception $error){
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }
}
