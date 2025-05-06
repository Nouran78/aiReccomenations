<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Throwable;
use OpenAI;
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

    public function aiRecommendations()
    {
        $client = \OpenAI::client(env('OPENAI_API_KEY'));

        // Path to your CSV file
        $csvPath = storage_path('app/sales.csv');

        // Upload the file to OpenAI (for Assistant tools)
        $file = $client->files()->upload([
            'purpose' => 'assistants',
            'file' => fopen($csvPath, 'r'),
        ]);

        // Use the uploaded file ID
        $fileId = $file->id;

        // Prepare your prompt
        $prompt = "Based on the sales data in the attached CSV, which products should we promote for increased revenue?";

        // Use GPT-4 with the file context (via tools, ideally with Assistants API)
        $response = $client->chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a data analyst.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            // This only works with the Assistants API that supports tool/file uploads.
            // If unsupported, you must fallback to reading and sending CSV content.
        ]);

        $recommendation = $response['choices'][0]['message']['content'] ?? 'No recommendation generated.';

        return response()->json([
            'file_id' => $fileId,
            'recommendation' => $recommendation,
        ]);
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
