<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Order::with('items')->latest()->get()
        ]);
    }

    public function placeOrder(Request $request)
    {
        $tableNumber = $request->input('table_number') ?? $request->input('table_name') ?? 'Bilinmiyor';
        $totalAmount = $request->input('total_amount') ?? $request->input('total_price') ?? 0;

        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        $order = Order::create([
            'table_number' => $tableNumber,
            'total_amount' => $totalAmount,
            'cutlery_requested' => $request->input('cutlery_requested', false),
            'payment_method' => $request->input('payment_method', 'cash'),
            'coupon_code' => $request->input('coupon_code'),
            'order_note' => $request->input('order_note') ?? $request->input('notes'),
            'status' => 'pending'
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'] ?? ('Ürün #' . $item['product_id']),
                'price' => $item['price'] ?? 0,
                'quantity' => $item['quantity']
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Siparişiniz başarıyla alındı ve mutfağa iletildi!',
            'data' => $order->load('items')
        ]);
    }
}