<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\CategoryBrand;
use App\Models\SubCategory;
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
        try {
            $brands = Brand::with('categoryBrands.subCategory', 'products')->get();
            return response()->json($brands, 200);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        try {
            $validatedData = $request->validate([
                'sub_category_id' => 'array|sometimes|exists:sub_categories,id',
                'brand_name' => 'required|string|max:255',
                'brand_description' => 'required|string',
                'brand_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($request->hasFile('brand_logo')) {
                $filePath = $request->file('brand_logo')->store('brands', 'public');
                $validatedData['brand_logo'] = URL::to(Storage::url($filePath));
            }

            $brand = Brand::create($validatedData);
            // dd($validatedData['sub_category_id']);
            foreach ($validatedData['sub_category_id'] as $sub_category) {
                $categoryBrand = CategoryBrand::create([
                    'brand_id' => $brand->id,
                    'sub_category_id' => $sub_category,
                ]);
            }
            return response()->json([
                'message' => 'Brand created successfully',
                'brand' => $brand
            ], 201);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $id)
    {
        try {
            $brand = Brand::where('id', $id->id)->with('products', 'subCategory')->first();
            return response()->json($brand, 200);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        try {
            $validatedData = $request->validate([
                'sub_category_id.*' => 'array',
                'sub_category_id' => 'sometimes|exists:sub_categories,id',
                'brand_name' => 'required|string|max:255',
                'brand_description' => 'required|string',
                'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($request->hasFile('brand_logo')) {
                // Delete the old logo if it exists
                if ($brand->brand_logo) {
                    Storage::disk('public')->delete($brand->brand_logo);
                }

                $filePath = $request->file('brand_logo')->store('brands', 'public');
                $validatedData['brand_logo'] = URL::to(Storage::url($filePath));
            }

            $brand->update($validatedData);

            // Update subcategories
            if (isset($validatedData['sub_category_id'])) {
                $brand->subCategories()->sync($validatedData['sub_category_id']);
            }

            return response()->json([
                'message' => 'Brand updated successfully',
                'brand' => $brand
            ], 200);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        try {
            if ($brand->brand_logo) {
                Storage::disk('public')->delete($brand->brand_logo);
            }

            $brand->delete();
            return response()->json([
                'message' => 'Brand deleted successfully',
            ], 200);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }
}
