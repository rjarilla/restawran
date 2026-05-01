<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IndexController extends Controller
{
    public function index()
    {
        $productColumns = Schema::getColumnListing('product');
        $usesProductRemaining = in_array('ProductQuantityRemaining', $productColumns, true);

        $products = DB::table('product')
            ->when(in_array('ProductStatus', $productColumns, true), function ($query) {
                $query->where(function ($statusQuery) {
                    $statusQuery->whereNull('ProductStatus')
                        ->orWhereIn('ProductStatus', ['Active', 'ACTIVE']);
                });
            })
            ->when($usesProductRemaining, function ($query) {
                $query->select('product.*', DB::raw('ProductQuantityRemaining as available_quantity'))
                    ->where('ProductQuantityRemaining', '>', 0);
            }, function ($query) {
                $today = now()->toDateString();
                $inventoryColumns = Schema::getColumnListing('productinventory');
                $inventoryStock = DB::table('productinventory')
                    ->select('ProductID', DB::raw('SUM(GREATEST(ProductQuantity, 0)) as available_quantity'))
                    ->where('ProductQuantity', '>', 0)
                    ->when(in_array('ProductBatchDeliveryDate', $inventoryColumns, true), function ($stockQuery) use ($today) {
                        $stockQuery->where(function ($dateQuery) use ($today) {
                            $dateQuery->whereNull('ProductBatchDeliveryDate')
                                ->orWhereDate('ProductBatchDeliveryDate', '<=', $today);
                        });
                    })
                    ->when(in_array('ProductBatchExpiry', $inventoryColumns, true), function ($stockQuery) use ($today) {
                        $stockQuery->whereDate('ProductBatchExpiry', '>=', $today);
                    })
                    ->groupBy('ProductID');

                $query->leftJoinSub($inventoryStock, 'stock', function ($join) {
                    $join->on('product.ProductID', '=', 'stock.ProductID');
                })
                    ->select('product.*', DB::raw('COALESCE(stock.available_quantity, 0) as available_quantity'))
                    ->where('stock.available_quantity', '>', 0);
            })
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
