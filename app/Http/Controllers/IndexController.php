<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index()
    {
        $products = DB::table('productinventory')
            ->join('product', 'productinventory.ProductID', '=', 'product.ProductID')
            ->where('productinventory.ProductQuantity', '>', 0)
            ->whereNotNull('productinventory.ProductBatchExpiry')
            ->where('productinventory.ProductBatchExpiry', '>', now())
            ->select('product.*', 'productinventory.ProductQuantity')
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return view('index', compact('products'));
    }
}