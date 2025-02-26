<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Market;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function updateMarket(Request $request, $id){
        try {
            $market = Market::findOrFail($id);
            $market->update($request->all());
            return response()->json(["message" => "updated successfully"], 200);
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()], $error->getCode());
        }
    }

    public function getAllMarkets(){
        try {
            $markets = Market::with('area', 'user')->get();
            return response()->json($markets, 200);
            } catch (\Exception $error) {
                return response()->json(['error' => $error->getMessage()], $error->getCode());
                }
    }

    public function getMarket(Request $request, $id){
        try {
            $market = Market::query()
            ->where('id',$id)
            ->with('user','area')
            ->get();
            return response()->json($market, 200);
            } catch (\Exception $error) {
                return response()->json(['error' => $error->getMessage()], $error->getCode());
                }
    }
}
