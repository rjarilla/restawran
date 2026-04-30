<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index()
    {
        $products = DB::table('productinventory')
            ->join('product', 'productinventory.ProductID', '=', 'product.ProductID')
            ->where('productinventory.ProductQuantity', '>', 0)
            ->where('productinventory.ProductBatchExpiry', '>', now())
            ->select('product.*', 'productinventory.ProductQuantity')
            ->inRandomOrder()
            ->limit(8)
            ->get();

        $customers = DB::table('customers')
            ->orderByDesc('CustomerUpdateDate')
            ->limit(10)
            ->get();

        return view('index', compact('products', 'customers'));
    }
}