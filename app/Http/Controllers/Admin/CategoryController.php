<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class CategoryController extends Controller
{
    public function index()
    {
        try{
            $category = Category::all();
            return response()->json($category, 200);
        }catch(\Exception $error){
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }

    public function show($id)
    {
        try {
            $category = Category::where('id', $id)->with('subCategory')->get();
            return response()->json($category, 200);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }

    public function store(Request $request)
    {

        try {
            $validatedData = $request->validate([
                'category_name' => 'required|string',
                'category_cover' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'description' => 'required|string',
            ]);

            // Handle image upload
            if ($request->hasFile('category_cover')) {
                $imagePath = $request->file('category_cover')->store('categories', 'public');
                $validatedData['category_cover'] = URL::to(Storage::url($imagePath));
            }

            $category = Category::create($validatedData);

            return response()->json(['message' => 'Category Created Successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function visibility($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->hide = !$category->hide;
            $category->save();

            return response()->json(['message' => 'Category visibility updated successfully']);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());
        try {
            $validatedData = $request->validate([
                'category_name' => 'required|string',
                'category_cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'description' => 'required|string',
            ]);

            // Find the category
            $category = Category::findOrFail($id);

            // Handle image upload if present
            if ($request->hasFile('category_cover')) {
                // Delete the old image if it exists
                if ($category->category_cover) {
                    Storage::disk('public')->delete($category->category_cover);
                }

                // Upload new image and update the path
                $imagePath = $request->file('category_cover')->store('categories', 'public');
                $validatedData['category_cover'] = URL::to(Storage::url($imagePath));
            }

            // Update category
            $category->update($validatedData);

            return response()->json(['message' => 'Category Updated Successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            if ($category->category_cover) {
                $imagePath = $category->category_cover;
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
