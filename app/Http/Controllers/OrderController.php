<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\RecommendationController;
class OrderController extends Controller
{
    protected $recommendationController;

    public function __construct(RecommendationController $recommendationController)
    {
        $this->recommendationController = $recommendationController;
    }

    public function index(Request $request)
    {

        $weatherData = $this->recommendationController->getWeather($request);

            $orders = DB::table('orders')
                ->select('id', 'product_id', 'quantity','type', 'price', 'adjusted_price', 'price_reason','created_at',   'updated_at','deleted_at')
                ->get();

            return view('orders.index', compact('orders'));


    }

    public function store(Request $request)
    {
        try {
            $product_id = $request->input('product_id');
            $quantity = $request->input('quantity');
            $price = $request->input('price');
            $type=$request->input('type');
            $date = now();

            if (!$product_id || !$quantity || !$price ||  !$type) {
                return response()->json(['error' => 'Missing fields'], 422);
            }

            DB::insert(
                'INSERT INTO orders (product_id, quantity, price,type, created_at) VALUES (?, ?,?,?, ?)',
                [$product_id, $quantity, $price,$type, $date]
            );

            $lastId = DB::getPdo()->lastInsertId();

            Cache::put('new_order', [
                'order_id' => $lastId,
                'product_id' => $product_id,
                'quantity' => $quantity,
                'price' => $price,
                'type' =>$type,
            ]);

            return response()->json(['message' => 'Order created and ready to broadcast via SSE'], 201);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $order = DB::table('orders')
                    ->where('id', $id)
                    ->whereNull('deleted_at')
                    ->first();

        if ($order) {
            return response()->json($order);
        } else {
            return response()->json(['message' => 'Order not found'], 404);
        }
    }

    public function destroy($id)
    {
        $orders = DB::table('orders')->where('id', $id)->first();

        if (!$orders) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Soft delete
        DB::table('orders')->where('id', $id)->update([
            'deleted_at' => now()
        ]);

        return view('orders.deleted',
        ['orders' => $orders,
        'success'=>'Order soft deleted successfully.'
    ]);
    }

    public function forceDelete($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        DB::table('orders')->where('id', $id)->delete();

        return response()->json(['message' => 'Order permanently deleted.'], 200);
    }

    public function restore($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        if (!$order || !$order->deleted_at) {
            return response()->json(['message' => 'Order not found or not deleted'], 404);
        }

        DB::table('orders')->where('id', $id)->update([
            'deleted_at' => null
        ]);

        return redirect()->back()->with('success', 'Order restored successfully.');

        // return response()->json(['message' => 'Order restored successfully.'], 200);
    }


    public function edit($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        return view('orders.edit', compact('order'));
    }
    public function update(Request $request, $id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        DB::table('orders')->where('id', $id)->update([
            'product_id' => $request->input('product_id'),
            'quantity' => $request->input('quantity'),
            'price' => $request->input('price'),

        ]);




return view('orders.edit', [
    'order' => $order,
    'success' => 'Order updated successfully.'
]);

    }

    public function deletedOrders()
    {
        $orders = DB::table('orders')
        ->whereNotNull('deleted_at')
        ->get();
        return view('orders.deleted', compact('orders'));
    }

    public function analytics()
    {
        $now = now();
        $oneMinuteAgo = now()->subMinute();

        $totalRevenue = DB::table('orders')
                        ->whereNull('deleted_at')
                        ->sum(DB::raw('quantity * price'));

        $topProducts = DB::select('
            SELECT product_id, SUM(quantity) as total_sold
            FROM orders
            WHERE deleted_at IS NULL
            GROUP BY product_id
            ORDER BY total_sold DESC
            LIMIT 5
        ');

        $recentRevenue = DB::table('orders')
                        ->whereNull('deleted_at')
                        ->where('created_at', '>=', $oneMinuteAgo)
                        ->sum(DB::raw('quantity * price'));

        $recentOrders = DB::table('orders')
                        ->whereNull('deleted_at')
                        ->where('created_at', '>=', $oneMinuteAgo)
                        ->count();

        return response()->json([
            'total_revenue' => $totalRevenue,
            'top_products' => $topProducts,
            'last_min_revenue' => $recentRevenue,
            'last_min_orders' => $recentOrders,
        ]);
    }


    public function streamOrders()
{
    return response()->stream(function () {
        while (true) {
            // Retrieve the latest order from the cache
            $order = Cache::get('new_order');

            if ($order) {
                echo "data: " . json_encode($order) . "\n\n";
                flush();
                Cache::forget('new_order');
            }

            sleep(1);
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    ]);
}
public function getLatestProducts()
{
    $products = DB::table('orders')
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();

    return response()->json($products);
}
public function getProductSuggestions(Request $request)
{
    $suggestedProducts = DB::table('orders')
                            ->inRandomOrder()
                            ->take(5)
                            ->get();

    return response()->json($suggestedProducts);
}

public function dashboard(){
    return view ('dashboard.index ');
}
}
