<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Str;

class SyncController
{
    public function getStatus()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'API is running and authenticated.'
        ]);
    }

    public function getTables()
    {
        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()->id;
        $tables = Table::where('restaurant_id', $restaurantId)->get();
        $orders = Order::with('items')->where('restaurant_id', $restaurantId)->where('status', 'pending')->get();

        return response()->json([
            'status' => 'success',
            'tables' => $tables,
            'active_orders' => $orders
        ]);
    }

    public function syncTables(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()->id;
        $request->validate([
            'tables' => 'required|array',
            'tables.*.name' => 'required|string',
        ]);

        foreach ($request->input('tables') as $tData) {
            $table = Table::where('name', $tData['name'])->where('restaurant_id', $restaurantId)->first();
            if (!$table) {
                Table::create([
                    'name' => $tData['name'],
                    'token' => Str::random(8),
                    'restaurant_id' => $restaurantId,
                    'active_session_id' => Str::random(40)
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Tables synced successfully.'
        ]);
    }

    public function placeOrUpdateOrder(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()->id;
        $request->validate([
            'table_name' => 'required|string',
            'items' => 'required|array',
            'items.*.product_name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer',
        ]);

        $tableName = $request->input('table_name');
        $table = Table::where('name', $tableName)->where('restaurant_id', $restaurantId)->first();
        if (!$table) {
            $table = Table::create([
                'name' => $tableName,
                'token' => Str::random(8),
                'restaurant_id' => $restaurantId,
                'active_session_id' => Str::random(40)
            ]);
        }

        $order = Order::create([
            'table_number' => $tableName,
            'total_amount' => collect($request->input('items'))->sum(fn($i) => $i['price'] * $i['quantity']),
            'status' => 'pending',
            'restaurant_id' => $restaurantId,
            'payment_method' => 'cash'
        ]);

        foreach ($request->input('items') as $item) {
            $prod = \App\Models\Product::where('name', $item['product_name'])->where('restaurant_id', $restaurantId)->first();
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $prod ? $prod->id : null,
                'product_name' => $item['product_name'],
                'price' => $item['price'],
                'quantity' => $item['quantity']
            ]);
        }

        return response()->json([
            'status' => 'success',
            'order_id' => $order->id
        ]);
    }

    public function settleOrder(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()->id;
        $request->validate([
            'table_name' => 'required|string',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric'
        ]);

        $tableName = $request->input('table_name');
        $paymentMethod = $request->input('payment_method');
        $amount = $request->input('amount');

        $activeOrders = Order::where('table_number', $tableName)
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'pending')
            ->get();

        $itemsSummary = [];
        foreach ($activeOrders as $o) {
            foreach ($o->items as $item) {
                $itemsSummary[] = $item->quantity . 'x ' . $item->product_name;
            }
            $o->status = 'completed';
            $o->save();
        }

        $table = Table::where('name', $tableName)->where('restaurant_id', $restaurantId)->first();
        if ($table) {
            $table->active_session_id = Str::random(40);
            $table->save();
        }

        Transaction::create([
            'restaurant_id' => $restaurantId,
            'table_name' => $tableName,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'details' => implode(', ', $itemsSummary)
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Table bill settled and synced successfully.'
        ]);
    }

    public function syncTransactions(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()->id;
        $request->validate([
            'transactions' => 'required|array',
        ]);

        foreach ($request->input('transactions') as $tData) {
            Transaction::create([
                'restaurant_id' => $restaurantId,
                'table_name' => $tData['table_name'] ?? 'Masa',
                'amount' => $tData['amount'],
                'payment_method' => $tData['payment_method'] ?? 'cash',
                'details' => $tData['details'] ?? '',
                'created_at' => isset($tData['created_at']) ? \Carbon\Carbon::parse($tData['created_at']) : now()
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Transactions synced successfully.'
        ]);
    }
}
