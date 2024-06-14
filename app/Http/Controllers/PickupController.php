<?php

namespace App\Http\Controllers;

use App\Models\Pickup;
use App\Models\Order;
use Illuminate\Http\Request;

class PickupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pickups = Pickup::all();
        return response()->json($pickups);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Return view if using Blade or simply return a response for APIs
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'pickup_time' => 'nullable|date',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'status' => 'nullable|string|max:50'
        ]);

        $pickup = Pickup::create([
            'order_id' => $request->order_id,
            'pickup_time' => $request->pickup_time,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'status' => $request->status ?? 'pending',
        ]);

        return response()->json($pickup, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pickup = Pickup::findOrFail($id);
        return response()->json($pickup);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Return view if using Blade or simply return a response for APIs
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'pickup_time' => 'nullable|date',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'status' => 'nullable|string|max:50'
        ]);

        $pickup = Pickup::findOrFail($id);
        $pickup->update([
            'order_id' => $request->order_id,
            'pickup_time' => $request->pickup_time,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'status' => $request->status ?? 'pending',
        ]);

        return response()->json($pickup);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pickup = Pickup::findOrFail($id);
        $pickup->delete();

        return response()->json(null, 204);
    }
}
