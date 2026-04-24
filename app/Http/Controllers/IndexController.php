<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index()
    {
        // Fetch 8 random products from productinventory where quantity > 0 and not expired, joined with products
        $products = DB::table('productinventory')
            ->join('product', 'productinventory.ProductID', '=', 'product.ProductID')
            ->where('productinventory.ProductQuantity', '>', 0)
            ->where('productinventory.ProductBatchExpiry', '>', now())
            ->select('product.*', 'productinventory.ProductQuantity')
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return view('index', compact('products'));
    }
}
