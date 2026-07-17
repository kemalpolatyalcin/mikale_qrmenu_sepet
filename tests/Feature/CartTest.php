<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use App\Models\Order;
use App\Models\OrderItem;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cart::clear();
    }

    public function test_cart_functionality()
    {
        Livewire::test('cart-drawer')
            ->call('addToCart', 1, 'Adana Kebap', 280)
            ->assertSet('discount', 0)
            ->call('updateQuantity', 1, 'increase')
            ->call('updateQuantity', 1, 'decrease')
            ->call('updateQuantity', 1, 'decrease');

        $this->assertTrue(Cart::isEmpty());
    }

    public function test_coupon_application()
    {
        Livewire::test('cart-drawer')
            ->call('addToCart', 1, 'Adana Kebap', 280)
            ->set('couponCode', 'INDIRIM10')
            ->call('applyCoupon')
            ->assertSet('discount', 10);
    }

    public function test_checkout()
    {
        Livewire::test('cart-drawer')
            ->set('tableNumber', 'Masa 5')
            ->call('addToCart', 1, 'Adana Kebap', 280)
            ->call('checkout')
            ->assertDispatched('order-success');

        $this->assertEquals(1, Order::count());
        $this->assertEquals(1, OrderItem::count());
        $this->assertEquals('Masa 5', Order::first()->table_number);
    }
}
