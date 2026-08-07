<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Table;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_place_order_via_api()
    {
        $table = Table::create([
            'name' => 'Masa 5',
            'token' => 'masa-5-token',
            'restaurant_id' => 1,
            'session_token' => 'abc123token',
            'session_expires_at' => now()->addMinutes(30)
        ]);

        $response = $this->postJson('/api/orders/place?token=abc123token', [
            'table_number' => 'Masa 5',
            'total_amount' => 360.00,
            'cutlery_requested' => true,
            'payment_method' => 'card',
            'order_note' => 'Acılı olsun',
            'items' => [
                [
                    'product_id' => 1,
                    'product_name' => 'Adana Kebap',
                    'price' => 280.00,
                    'quantity' => 1
                ],
                [
                    'product_id' => 2,
                    'product_name' => 'Lahmacun',
                    'price' => 80.00,
                    'quantity' => 1
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success'
        ]);

        $this->assertDatabaseHas('orders', [
            'table_number' => 'Masa 5',
            'total_amount' => 360.00,
            'cutlery_requested' => true,
            'payment_method' => 'card',
            'order_note' => 'Acılı olsun'
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => 1,
            'product_name' => 'Adana Kebap',
            'price' => 280.00,
            'quantity' => 1
        ]);
    }

    public function test_can_fetch_orders_via_api()
    {
        $table = Table::create([
            'name' => 'Masa 2',
            'token' => 'masa-2-token',
            'restaurant_id' => 1,
            'session_token' => 'abc456token',
            'session_expires_at' => now()->addMinutes(30)
        ]);

        $this->postJson('/api/orders/place?token=abc456token', [
            'table_number' => 'Masa 2',
            'total_amount' => 80.00,
            'items' => [
                [
                    'product_id' => 2,
                    'product_name' => 'Lahmacun',
                    'price' => 80.00,
                    'quantity' => 1
                ]
            ]
        ]);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success'
        ]);
        $response->assertJsonCount(1, 'data');
    }
}
