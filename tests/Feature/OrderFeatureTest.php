<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFeatureTest extends TestCase
{
    /** @test */
    public function it_returns_orders_index()
    {
        $response = $this->get('/api/orders');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_creates_an_order()
    {
        $payload = [
            'product_id' => 1,
            'quantity' => 2,
            'price' => 49.99,
            'status' => 'pending'
        ];

        $response = $this->post('/api/orders', $payload);

        $response->assertStatus(201); // or 200 depending on your controller logic
        $response->assertJsonStructure([
            'product_id', 'quantity', 'price', 'status'
        ]);
    }

    /** @test */
    public function it_returns_single_order_if_exists()
    {
        // Pretend the order with ID 1 exists in the test DB or mock it
        $response = $this->get('/api/orders/1');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_returns_404_for_missing_order()
    {
        $response = $this->get('/api/orders/999999');
        $response->assertStatus(404);
    }
}
