<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MenuController extends Controller
{
    public function getCategories()
    {
        $categories = Schema::hasTable('categories') ? Category::all() : collect();
        return response()->json(['status' => 'success', 'data' => $categories]);
    }

    public function getProducts()
    {
        $products = Schema::hasTable('products') ? Product::all() : collect();
        return response()->json(['status' => 'success', 'data' => $products]);
    }

    public function getUser()
    {
        return response()->json(['status' => 'success', 'data' => ['name' => 'Kemal Polat']]);
    }

    public function getTable($token)
    {
        if (!Schema::hasTable('tables')) {
            return response()->json(['status' => 'error', 'message' => 'Table not found'], 404);
        }
        $table = \App\Models\Table::where('token', $token)->first();
        if ($table) {
            return response()->json(['status' => 'success', 'data' => ['name' => $table->name]]);
        }
        return response()->json(['status' => 'error', 'message' => 'Table not found'], 404);
    }
}