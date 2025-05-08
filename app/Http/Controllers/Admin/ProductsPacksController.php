<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductsPacks;

class ProductsPacksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packs = ProductsPacks::all();
        return response()->json($packs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pack_name' => 'required|string|max:255',
            'pack_size' => 'required|string|max:255',
        ]);

        $pack = ProductsPacks::create($request->all());

        return response()->json($pack, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'pack_name' => 'sometimes|required|string|max:255',
            'pack_size' => 'sometimes|required|string|max:255',
        ]); 

        $pack = ProductsPacks::findOrFail($id);
        $pack->update($request->all());

        return response()->json($pack);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pack = ProductsPacks::findOrFail($id);
        $pack->delete();

        return response()->json(null, 204);
    }
}
