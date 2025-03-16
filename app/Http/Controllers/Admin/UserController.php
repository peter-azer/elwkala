<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignedOrders;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of users with 'admin' or 'super admin' roles.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing the list of users.
     */
    public function index()
    {
        $users = User::role(['admin', 'super admin'])
            ->with('roles')
            ->get();
        return response()->json($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getUserMarkets(Request $request, $id)
    {
        try {
            $usersMarkets = User::query()
                ->where('id', $id)
                ->with('market')
                ->get();
            return response()->json($usersMarkets);
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $users = User::findOrFail($id);
            $users->update($request->all());
            return response()->json(["message" => "update successfully"], 200);
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()], $error->getCode());
        }
    }

    public function assign(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'market_id' => 'required|exists:markets,id',
                'order_id' => 'required|string'
            ]);

            $assign = AssignedOrders::create($validatedData);
            return response()->json(["message" => "update successfully"], 200);
        } catch (\Exception $error) {
            return response()->json(['message', $error->getMessage()], $error->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
