<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;

class MenuController extends Controller
{
    public function getCategories()
    {
        $categories = Category::orderBy('id', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }
    public function getProducts()
    {

        $products = Product::all();

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