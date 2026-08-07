<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Tests\TestCase;

class ManagerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_page_loads_successfully()
    {
        $response = $this->get('/manager');
        $response->assertStatus(200);
        $response->assertSee('Masa Görünümü');
    }

    public function test_manager_can_settle_bill()
    {
        $table = Table::create([
            'name' => 'Masa 4',
            'token' => 'masa-4-token',
            'restaurant_id' => 1
        ]);

        $order = Order::create([
            'table_number' => 'Masa 4',
            'total_amount' => 150.00,
            'status' => 'pending',
            'restaurant_id' => 1
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => 1,
            'product_name' => 'Adana Kebap',
            'price' => 150.00,
            'quantity' => 1
        ]);

        Livewire::test('manager-dashboard')
            ->assertSee('Masa 4')
            ->call('selectTable', 'Masa 4')
            ->call('startSettlement')
            ->call('settleBill');

        $this->assertEquals('completed', $order->fresh()->status);
    }
}
