<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\OrderDetails;
use Illuminate\Support\Facades\DB;

class InventoryMovementReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Product::query()
            ->select([
                'product.ProductID',
                'product.ProductName',
                'product.ProductDescription',
                DB::raw('COALESCE(SUM(pi.ProductQuantity),0) as total_in'),
                DB::raw('COALESCE(SUM(od.OrderQuantity),0) as total_out'),
                DB::raw('COALESCE(SUM(pi.ProductQuantity),0) - COALESCE(SUM(od.OrderQuantity),0) as current_stock')
            ])
            ->leftJoin('productinventory as pi', function($join) use ($startDate, $endDate) {
                $join->on('product.ProductID', '=', 'pi.ProductID');
                if ($startDate) $join->where('pi.ProductBatchDeliveryDate', '>=', $startDate);
                if ($endDate) $join->where('pi.ProductBatchDeliveryDate', '<=', $endDate);
            })
            ->leftJoin('orderdetails as od', function($join) use ($startDate, $endDate) {
                $join->on('product.ProductID', '=', 'od.ProductID');
                if ($startDate) $join->where('od.created_at', '>=', $startDate);
                if ($endDate) $join->where('od.created_at', '<=', $endDate);
            })
            ->groupBy('product.ProductID', 'product.ProductName', 'product.ProductDescription');

        $products = $query->get();

        return view('admin.reports.inventory_movement', [
            'products' => $products,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }
}
