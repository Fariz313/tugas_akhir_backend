<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'user') {
            $orders = Order::where('user_id', $user->id)->get();
        } else {
            $orders = Order::paginate();
        }

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'address' => 'required|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'date' => 'nullable|date',
            'day' => 'nullable|integer',
            'stopped_at' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $date =null;
        if($request->type=='one-time'){ 
            $date=date("Y-m-d");
        }

        $order = Order::create([
            'user_id' => Auth::id(), // Get the authenticated user ID
            'type' => $request->type,
            'address' => $request->address,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'date' => $date,
            'day' => $request->day,
            'stopped_at' => $request->stopped_at,
            'status' => $request->status ?? 'pending',
        ]);

        return response()->json($order, 201);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'sometimes|string',
            'address' => 'sometimes|string',
            'lat' => 'sometimes|nullable|numeric',
            'lng' => 'sometimes|nullable|numeric',
            'date' => 'sometimes|nullable|date',
            'day' => 'sometimes|nullable|integer',
            'stopped_at' => 'sometimes|nullable|date',
            'status' => 'sometimes|string',
        ]);

        $order = Order::findOrFail($id);
        $order->update($request->all());

        return response()->json($order);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
