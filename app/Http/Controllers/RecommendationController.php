<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
class RecommendationController extends Controller
{

//     public function getWeather(Request $request)
//     {
//         $apiKey = '90c92640c35d45a9a42191957252704';

//         $ip = request()->ip();


// if ($ip == '127.0.0.1' || $ip == '::1') {
//     $city = 'Cairo';
// } else {
//     $geo = Http::get("http://ip-api.com/json/{$ip}")->json();
//     $city = $geo['city'] ?? 'Cairo';
// }

//         $response = Http::get("http://api.weatherapi.com/v1/current.json", [
//             'key' => $apiKey,
//             'q' => $city,
//         ]);

//         if ($response->successful()) {
//             $weather = $response->json();
//             $temp = $weather['current']['temp_c'];
//             $condition = $weather['current']['condition']['text'];

//             $recommendation = $temp > 25 ? 'Cold Drinks (like Iced Coffee, Smoothies 🍹)' : 'Hot Drinks (like Cappuccino, Tea ☕)';

//             // Dynamic Pricing
//             $baseColdDrinkPrice = 120;
//             $baseHotDrinkPrice = 100;

//             $coldDrinkPrice = $baseColdDrinkPrice;

//             $hotDrinkPrice = $baseHotDrinkPrice;

//             if ($temp > 25) {
//                 $coldDrinkPrice *= 1.10;
//             } elseif ($temp < 15) {
//                 $hotDrinkPrice *= 1.10;
//             }

//             return [
//                 'temperature' => $temp,
//                 'condition' => $condition,
//                 'city' => $city,
//                 'recommendation' => $recommendation,
//                 'coldDrinkPrice' => number_format($coldDrinkPrice, 2),
//                 'hotDrinkPrice' => number_format($hotDrinkPrice, 2),
//             ];


//         } else {
//             return response()->json(['error' => 'Unable to fetch weather data'], 500);
//         }
//     }



public function getWeather(Request $request)
{
    $apiKey = '90c92640c35d45a9a42191957252704';
    $ip = request()->ip();

    $city = ($ip === '127.0.0.1' || $ip === '::1') ? 'Cairo' : (Http::get("http://ip-api.com/json/{$ip}")->json()['city'] ?? 'Cairo');

    $response = Http::get("http://api.weatherapi.com/v1/current.json", [
        'key' => $apiKey,
        'q' => $city,
    ]);

    if (!$response->successful()) {
        return response()->json(['error' => 'Unable to fetch weather data'], 500);
    }

    $weather = $response->json();
    $temp = $weather['current']['temp_c'];
    $condition = $weather['current']['condition']['text'];

    $category = $temp > 25 ? 'cold' : 'hot';

    // Fetch drinks of the recommended category from your products or drinks table
    $recommendedDrinks = DB::table('products')
    ->where('category', $category)
    ->select('id', 'name', 'base_price')
    ->limit(5)
    ->get()
    ->map(function ($drink) use ($temp, $category) {
        if (($category === 'cold' && $temp > 25) || ($category === 'hot' && $temp < 15)) {
            $drink->base_price = number_format($drink->base_price * 1.10, 2);
        }
        return $drink;
    });

return response()->json([
    'temperature' => $temp,
    'condition' => $condition,
    'city' => $city,
    'recommendationcategory' => $category,
    'recommendedDrinks' => $recommendedDrinks
]);
}

    public function aiRecommendations()
    {
        $sales = DB::table('sales_summary')
            ->join('products', 'sales_summary.product_id', '=', 'products.id')
            ->select(
                'products.name as product',
                'sales_summary.total_quantity',
                'sales_summary.total_revenue',
                'sales_summary.adjusted_total_revenue',
                'sales_summary.orders_count',
                'sales_summary.revenue_change_pct',
                'sales_summary.adjusted_revenue_change_pct'
            )
            ->get();

        $csvContent = "Product,Quantity Sold,Revenue,Adjusted Revenue,Orders,Revenue %,Adjusted Revenue %\n";
        foreach ($sales as $row) {
            $csvContent .= "{$row->product},{$row->total_quantity},{$row->total_revenue},{$row->adjusted_total_revenue},{$row->orders_count},{$row->revenue_change_pct},{$row->adjusted_revenue_change_pct}\n";
        }

        $prompt = "Based on this sales analytics data, which products should we promote to increase revenue?\n\n" . $csvContent;

        $response = Http::post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . env('GOOGLE_API_KEY'),
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]
        );


        $data = $response->json();
        // dd($data);

        $recommendation = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No recommendation generated.';

        return response()->json([
            'recommendation' => $recommendation,
        ]);
    }


    public function home()
    {
        $weatherData = $this->getWeather(request());
        return response()->json(['weatherData' => $weatherData]);
    }





}
