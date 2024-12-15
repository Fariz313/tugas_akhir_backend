<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'user') {
            if($request->has('page')){
                $orders = Order::where('user_id', $user->id)->paginate();
            }else{
                $orders = Order::select('orders.*','pickup_time')->where('orders.status','running')->where('user_id', $user->id)->leftJoin('pickups','pickups.order_id','orders.id')->get();
            }
        } else if($user->role === 'driver'){
            $today = Carbon::today(); // Get today's date
            $todayDayOfWeek = $today->dayOfWeek; // 0 (Sunday) to 6 (Saturday)
            $orders = DB::table('orders')
            ->select('orders.*', 'users.name','pickups.pickup_time as pickups_time')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('pickups','pickups.order_id','orders.id')
            ->where(function ($query) use ($today, $todayDayOfWeek) {
                $query->where(function ($subQuery) use ($today) {
                    // One-time orders with today's date
                    $subQuery->where('orders.type', 'one-time')
                             ->whereDate('orders.date', $today);
                })
                ->orWhere(function ($subQuery) use ($todayDayOfWeek) {
                    // Subscription orders with today's day of the week
                    $subQuery->where('orders.type', 'subscription')
                             ->where('orders.day', $todayDayOfWeek);
                });
            })
            ->get();
        }else {
            $orders = Order::paginate();
        }

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'address' => 'required|string',
            'multilatlng' => 'sometimes|nullable|string',
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
            'multilatlng' => $request->multilatlng,
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
            'multilatlng' => 'sometimes|nullable|string',
            'date' => 'sometimes|nullable|date',
            'day' => 'sometimes|nullable|integer',
            'stopped_at' => 'sometimes|nullable|date',
            'status' => 'sometimes|string',
        ]);

        $order = Order::findOrFail($id);
        $order->update($request->all());

        return response()->json($order);
    }
    public function updateData(Request $request)
    {
        
        $id = $request->input('id');
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
