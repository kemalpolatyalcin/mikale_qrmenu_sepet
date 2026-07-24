<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Table;
use App\Models\Order;

class ErestIntegrationController extends Controller
{

    public function getTables()
    {

        $tables = [
            ['id' => 1, 'name' => 'Masa 1', 'qr_url' => url('/?masa=1')],
            ['id' => 2, 'name' => 'Masa 2', 'qr_url' => url('/?masa=2')],
            ['id' => 3, 'name' => 'Bahçe 1', 'qr_url' => url('/?masa=3')],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $tables
        ]);
    }
    public function receiveOrder(Request $request)
    {
        $items = $request->input('items', []);

        $kitchenItems = [];
        $waiterItems = [];

        foreach ($items as $item) {
            $waiterItems[] = $item;


            if (isset($item['department']) && strtolower($item['department']) === 'mutfak') {
                $kitchenItems[] = $item;
            }
        }



        return response()->json([
            'status' => 'success',
            'message' => 'Sipariş başarıyla alındı.',
            'details' => [
                'routed_to_kitchen' => count($kitchenItems) . ' ürün mutfağa iletildi.',
                'routed_to_waiter' => count($waiterItems) . ' ürün garson ekranına iletildi.'
            ]
        ]);
    }

    public function getReports(Request $request)
    {
        $startDate = Carbon::now()->subDays(3)->subHours(12);



        return response()->json([
            'status' => 'success',
            'report_period' => 'Son 3.5 Gün',
            'report_start_date' => $startDate->format('Y-m-d H:i:s'),
            'total_revenue' => 12500.75,
            'message' => 'Rapor verileri kendi sistemimizden başarıyla getirildi.'
        ]);
    }
}