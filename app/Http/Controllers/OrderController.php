<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Events\NewOrderCreated;
use App\Events\TopSellingUpdated;
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
            ->select('id', 'product_id', 'quantity', 'type', 'price', 'adjusted_price', 'price_reason', 'created_at', 'updated_at', 'deleted_at')
            ->get();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        try {
            $product_id = $request->input('product_id');
            $quantity = $request->input('quantity');
            $price = $request->input('price');
            $type = $request->input('type');
            $date = now();

            if (!$product_id || !$quantity || !$price || !$type) {
                return response()->json(['error' => 'Missing fields'], 422);
            }

            // Weather API
            $apiKey = '90c92640c35d45a9a42191957252704';
            $ip = request()->ip();

            $city = ($ip === '127.0.0.1' || $ip === '::1') ? 'Cairo' : (Http::get("http://ip-api.com/json/{$ip}")->json()['city'] ?? 'Cairo');

            $weatherResponse = Http::get("http://api.weatherapi.com/v1/current.json", [
                'key' => $apiKey,
                'q' => $city,
            ]);

            $adjusted_price = $price;
            $price_reason = null;

            if ($weatherResponse->successful()) {
                $weather = $weatherResponse->json();
                $temp = $weather['current']['temp_c'];

                if ($temp > 25 && $type === 'cold') {
                    $adjusted_price = round($price * 1.10, 2);
                    $price_reason = 'Hot weather';
                } elseif ($temp < 15 && $type === 'hot') {
                    $adjusted_price = round($price * 1.10, 2);
                    $price_reason = 'Cold weather';
                }
            }
        DB::insert(
            'INSERT INTO orders (product_id, quantity, price, adjusted_price, price_reason, type, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$product_id, $quantity, $price, $adjusted_price, $price_reason, $type, $date]
        );

        $lastId = DB::getPdo()->lastInsertId();

        $orderData = [
            'order_id' => $lastId,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $price,
            'adjusted_price' => $adjusted_price,
            'price_reason' => $price_reason,
            'type' => $type,
        ];

        // 🔥 Broadcast the event
        event(new NewOrderCreated($orderData));

        return response()->json(['message' => 'Order created and broadcasted'], 201);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    }


    public function promoteTopSelling()
    {
        $topProduct = DB::table('orders')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->first();

        if ($topProduct) {
            // You can fetch full product info if you have a products table
            $product = DB::table('products')->where('id', $topProduct->product_id)->first();

            event(new TopSellingUpdated($product->name, $product->id));

            return response()->json(['message' => 'Top selling product promoted.']);
        }

        return response()->json(['message' => 'No orders found.'], 404);
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
        DB::table('orders')->where('id', $id)->update(['deleted_at' => now()]);
        return response()->json(['message' => 'Order soft deleted successfully.']);
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
            return response()->json(['message' => 'Order not found.'], 404);
        }
        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
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
        return response()->json(['message' => 'Order updated successfully.']);
    }

    public function deletedOrders()
    {
        $orders = DB::table('orders')->whereNotNull('deleted_at')->get();
        return response()->json($orders);
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
