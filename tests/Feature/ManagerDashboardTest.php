<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Models\Order;
use App\Models\OrderItem;
use Tests\TestCase;

class ManagerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_page_loads_successfully()
    {
        $response = $this->get('/manager');
        $response->assertStatus(200);
        $response->assertSee('Masa Sipariş Yönetim Paneli');
    }

    public function test_manager_can_update_order_status()
    {
        $order = Order::create([
            'table_number' => 'Masa 4',
            'total_amount' => 150.00,
            'status' => 'pending'
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
            ->assertSee('Bekliyor')
            ->call('updateStatus', $order->id, 'preparing');

        $this->assertEquals('preparing', $order->fresh()->status);
    }
}
