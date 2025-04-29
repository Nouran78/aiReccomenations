<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Throwable;
class RecommendationController extends Controller
{

    public function getWeather(Request $request)
    {
        $apiKey = '90c92640c35d45a9a42191957252704';

        $ip = request()->ip();


if ($ip == '127.0.0.1' || $ip == '::1') {
    $city = 'Cairo';
} else {
    $geo = Http::get("http://ip-api.com/json/{$ip}")->json();
    $city = $geo['city'] ?? 'Cairo';
}

        $response = Http::get("http://api.weatherapi.com/v1/current.json", [
            'key' => $apiKey,
            'q' => $city,
        ]);

        if ($response->successful()) {
            $weather = $response->json();
            $temp = $weather['current']['temp_c'];
            $condition = $weather['current']['condition']['text'];

            $recommendation = $temp > 25 ? 'Cold Drinks (like Iced Coffee, Smoothies 🍹)' : 'Hot Drinks (like Cappuccino, Tea ☕)';

            // Dynamic Pricing
            $baseColdDrinkPrice = 120;
            $baseHotDrinkPrice = 100;

            $coldDrinkPrice = $baseColdDrinkPrice;
            $hotDrinkPrice = $baseHotDrinkPrice;

            if ($temp > 25) {
                $coldDrinkPrice *= 1.10;
            } elseif ($temp < 15) {
                $hotDrinkPrice *= 1.10;
            }

            return [
                'temperature' => $temp,
                'condition' => $condition,
                'city' => $city,
                'recommendation' => $recommendation,
                'coldDrinkPrice' => number_format($coldDrinkPrice, 2),
                'hotDrinkPrice' => number_format($hotDrinkPrice, 2),
            ];


        } else {
            return response()->json(['error' => 'Unable to fetch weather data'], 500);
        }
    }
    public function home()
{
    $weatherData = $this->getWeather(request());

    // $aiRecommendations = $this->getRecommendations()->getData(true);


    return view('home.index', [
        'weatherData' => $weatherData,
        // 'aiRecommendations' => $aiRecommendations,
    ]);
}

}
