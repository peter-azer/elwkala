<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $brands = Brand::all();
            return response()->json($brands, 200);
        }catch(\Exception $error){
            return response()->json(['message' => $error->getMessage()]);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        try{
            $validatedData = $request->validate([
                'category_id' => 'sometimes|exists:categories,id',
                'brand_name' => 'required|string|max:255',
                'brand_description' => 'required|sting',
                'brand_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if($request->hasFile('brand_logo')){
                $filePath = $request->file('brand_lgo')->store('brands', 'public');
                $validatedData['brand_logo'] = URL::to(Storage::url($filePath));
            }

            $brand = Brand::create($validatedData);
            return response()->json([
                'message' => 'Brand created successfully',
                'brand' => $brand
            ], 201);
        }catch(\Exception $error){
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        try{
            $brand = Brand::where('id', $brand->id)->with('products', 'category')->first();
            return response()->json($brand, 200);
        }catch(\Exception $error){
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        try{
            $validatedData = $request->validate([
                'category_id' => 'sometimes|exists:categories,id',
                'brand_name' => 'required|string|max:255',
                'brand_description' => 'required|text',
                'brand_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            if($request->hasFile('brand_logo')){
                if($brand->brand_logo){
                    Storage::disk('public')->delete($brand->brand_logo);
                }
                $filePath = $request->file('brand_logo')->store('brands', 'public');
                $validatedData['brand_logo'] = URL::to(Storage::url($filePath));
            }
            $brand->update($validatedData);
            return response()->json([
                'message' => 'Brand updated successfully',
                'brand' => $brand
            ], 200);
        }catch(\Exception $error){
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        try{
            if($brand->brand_logo){
                Storage::disk('public')->delete($brand->brand_logo);
            }

            $brand->delete();
            return response()->json([
                'message' => 'Brand deleted successfully',
            ], 200);
        }catch(\Exception $error){
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }
}
