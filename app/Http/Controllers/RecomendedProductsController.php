<?php

namespace App\Http\Controllers;

use App\Models\RecomendedProducts;
use App\Http\Requests\StoreRecomendedProductsRequest;
use App\Http\Requests\UpdateRecomendedProductsRequest;

class RecomendedProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecomendedProductsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(RecomendedProducts $recomendedProducts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RecomendedProducts $recomendedProducts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecomendedProductsRequest $request, RecomendedProducts $recomendedProducts)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RecomendedProducts $recomendedProducts)
    {
        //
    }
}
