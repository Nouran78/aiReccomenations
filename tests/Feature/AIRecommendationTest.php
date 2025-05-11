<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class AIRecommendationTest extends TestCase
{
    public function test_recommendation_endpoint_returns_ai_response()
    {
        // Fake sales.csv
        Storage::fake();
        Storage::put('sales.csv', "product,amount\nA,200\nB,400\nC,100");

        // Fake HTTP response from Gemini
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Promote product B for best revenue increase.']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->getJson('/api/recommendations');

        $response->assertStatus(200);
        $response->assertJson([
            'recommendation' => 'Promote product B for best revenue increase.'
        ]);
    }

    public function test_recommendation_endpoint_handles_missing_file()
    {
        Storage::fake();

        $response = $this->getJson('/api/recommendations');

        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'Sales data not found.'
        ]);
    }
}

