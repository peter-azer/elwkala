<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::role(['admin', 'super admin'])->get();
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
        try {
            $users = User::findOrFail($id);
            $users->update($request->all());
            return response()->json(["message" => "update successfully"], 200);
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()], $error->getCode());
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
