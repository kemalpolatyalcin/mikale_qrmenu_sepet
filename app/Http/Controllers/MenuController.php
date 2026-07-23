<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use App\Models\Restaurant;

class MenuController extends Controller
{
    public function getCategories(Request $request)
    {
        $tableToken = $request->query('masa') ?? $request->query('table');
        $restaurantId = null;
        if ($tableToken) {
            $table = Table::where('token', $tableToken)->first();
            if ($table) {
                $restaurantId = $table->restaurant_id;
            }
        }
        if (!$restaurantId) {
            $first = Restaurant::first();
            $restaurantId = $first ? $first->id : null;
        }

        $categories = Category::where('restaurant_id', $restaurantId)->orderBy('id', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }
    public function getProducts(Request $request)
    {
        $tableToken = $request->query('masa') ?? $request->query('table');
        $restaurantId = null;
        if ($tableToken) {
            $table = Table::where('token', $tableToken)->first();
            if ($table) {
                $restaurantId = $table->restaurant_id;
            }
        }
        if (!$restaurantId) {
            $first = Restaurant::first();
            $restaurantId = $first ? $first->id : null;
        }

        $products = Product::where('restaurant_id', $restaurantId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }
    public function getTable($token)
    {
        $table = Table::where('token', $token)->first();
        if ($table) {
            return response()->json([
                'status' => 'success',
                'data' => $table
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Table not found'
        ], 404);
    }
}