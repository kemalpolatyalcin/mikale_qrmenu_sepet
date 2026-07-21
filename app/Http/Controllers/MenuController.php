<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

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
}