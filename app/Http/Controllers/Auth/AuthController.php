<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Market;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required|string',
        ]);
        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid phone or password'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'userName' => $user->name,
            'market' => $user->market()->get(),
            'role' => $user->getRoleNames()
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function registerUser(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string',
                'role' => 'required|string|exists:roles,name',
                'password' => 'required|string|min:8',
            ]);
            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => $request->password
            ]);
            $user->assignRole($request->role);
        } catch (\Exception $error) {
            return response()->json(['message' => $error], 500);
        }
    }
    public function registerMarket(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'integer|exists:users,id',
                'area_id' => 'integer|exists:areas,id',
                'owner_name' => 'required|string|max:255',
                'manager_name' => 'required|string|max:255',
                'market_name' => 'required|string|max:255',
                'phone2' => 'required|string',
                'phone3' => 'required|string',
                'address' => 'required|string',
                'max_order_quantity' => 'required|integer'
            ]);
            $market = Market::create([
                'user_id' => $request->user_id,
                'area_id' => $request->area_id,
                'owner_name' => $request->owner_name,
                'manager_name' => $request->manager_name,
                'market_name' => $request->market_name,
                'phone2' => $request->phone2,
                'phone3' => $request->phone3,
                'max_order_quantity' => $request->max_order_quantity,
                'address' => $request->address
            ]);
            $market->assignRole($request->role);
        } catch (\Exception $error) {
            return response()->json(["message" => $error], $error->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
