<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $category = SubCategory::with('category')->get();
            return response()->json($category, 200);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'category_id' => 'required|integer|exists:categories,id',
                'sub_category_cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Handle image upload
            if ($request->hasFile('sub_category_cover')) {
                $imagePath = $request->file('sub_category_cover')->store('sub_categories', 'public');
                $validatedData['sub_category_cover'] = URL::to(Storage::url($imagePath));
            }

            $category = SubCategory::create($validatedData);

            return response()->json(['message' => 'Category Created Successfully']);
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
            $category = SubCategory::where('id', $id)->with('categoryBrands.brand', 'products')->get();
            return response()->json($category, 200);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        // dd($request->all());
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|string',
                'category_id' => 'sometimes|integer|exists:categories,id',
                'sub_category_cover' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Find the category
            $category = SubCategory::findOrFail($id);

            // Handle image upload if present
            if ($request->hasFile('sub_category_cover')) {
                // Delete the old image if it exists
                if ($category->sub_category_cover) {
                    Storage::disk('public')->delete($category->sub_category_cover);
                }
                // Upload new image and update the path
                $imagePath = $request->file('sub_category_cover')->store('sub_categories', 'public');
                $validatedData['sub_category_cover'] = URL::to(Storage::url($imagePath));
            }

            // Update category
            $category->update($validatedData);

            return response()->json(['message' => 'Category Updated Successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = SubCategory::findOrFail($id);
            if ($category->sub_category_cover) {
                $imagePath = $category->sub_category_cover;
                // Remove domain and '/storage' prefix
                $cleanPath = Str::replaceFirst('/storage', '', parse_url($imagePath, PHP_URL_PATH));
                // Delete the image from the public disk
                Storage::disk('public')->delete($cleanPath);
            }
            $category->delete();
            return response()->json(["message" => "Deleted Successfully"]);
        } catch (\Exception $error) {
            return response()->json(["message" => $error->getMessage()], $error->getCode());
        }
    }
}
