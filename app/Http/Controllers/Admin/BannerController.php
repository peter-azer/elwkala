<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::with('product')->get();
        return response()->json($banners);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBannerRequest $request)
    {
        try{
            $validatedData = $request->validate([
                'product_id' => 'required|exists:products,id',
                'image_url' => 'required|image|max:2048',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ]);
            
            // Handle file upload for image_url
            if ($request->hasFile('image_url')) {
                $imagePath = $request->file('image_url')->store('banners', 'public');
                $validatedData['image_url'] = URL::to(Storage::url($imagePath));
            }
            Banner::create($validatedData);
            return response()->json(['message' => 'Banner created successfully.'], 201);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        try {

            $validatedData = $request->validate([
                'product_id' => 'sometimes|exists:products,id',
                'image_url' => 'sometimes|image|max:2048',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ]);
            
            // Handle file upload for image_url
            if ($request->hasFile('image_url')) {
                $imagePath = $request->file('image_url')->store('banners', 'public');
                $validatedData['image_url'] = URL::to(Storage::url($imagePath));
            }
            $banner->update($validatedData);
            return response()->json(['message' => 'Banner Updated successfully.'], 200);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        try{
            $banner->delete();
            return response()->json(['message' => 'Banner deleted successfully.'], 201);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }
}
