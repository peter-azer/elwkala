<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $categories = Category::where('hide', false)
            ->orderBy('order', 'asc')
            ->get();
            return response()->json($categories);
        }catch(\Exception $error){
            return response()->json(['message'=> $error->getMessage()], 500);

        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try{
            $category = Category::query()
            ->where('id',$id)
            ->with('subCategory')
            ->get();
            return response()->json($category);
        }catch(\Exception $error){
            return response()->json(['message'=>$error->getMessage()], 500);
        }
    }

}
