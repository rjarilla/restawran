<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Customer Purchase Report Data
        $filterType = $request->input('filter_type', 'month');
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Orders::with(['customer', 'payment'])
            ->whereHas('payment')
            ->has('orderDetails');

        if ($filterType === 'month') {
            $query->whereMonth('OrderDate', $month)
                  ->whereYear('OrderDate', $year);
        } elseif ($filterType === 'year') {
            $query->whereYear('OrderDate', $year);
        } elseif ($filterType === 'range' && $dateFrom && $dateTo) {
            $query->whereBetween('OrderDate', [$dateFrom, $dateTo]);
        }

        $orders = $query->orderByDesc('OrderDate')->get();

        $revenue = $orders->sum(function ($order) {
            return $order->payment ? $order->payment->PaymentTotal : $order->OrderTotalAmount;
        });

        $customerStats = $orders->groupBy('CustomerID')->map(function ($orders, $customerId) {
            $customer = $orders->first()->customer;
            $totalSpent = $orders->sum(function ($order) {
                return $order->payment ? $order->payment->PaymentTotal : $order->OrderTotalAmount;
            });

            return (object) [
                'CustomerID' => $customerId,
                'CustomerName' => $customer->CustomerName ?? 'Unknown',
                'TotalPurchases' => $orders->count(),
                'TotalSpent' => $totalSpent,
                'Transactions' => $orders->count(),
                'LastOrderDate' => optional($orders->sortByDesc('OrderDate')->first())->OrderDate,
            ];
        })->sortByDesc('TotalSpent');

        $topCustomers = $customerStats->take(5);

        $summary = [
            'totalOrders' => $orders->count(),
            'totalRevenue' => $revenue,
            'uniqueCustomers' => $customerStats->count(),
        ];

        // Daily Sales Report Data
        $dailySales = $orders->groupBy('OrderDate')->map(function ($dayOrders, $date) {
            $totalRevenue = $dayOrders->sum(function ($order) {
                return $order->payment ? $order->payment->PaymentTotal : $order->OrderTotalAmount;
            });
            return [
                'date' => $date,
                'total' => $totalRevenue,
                'average' => $dayOrders->count() > 0 ? $totalRevenue / $dayOrders->count() : 0,
            ];
        })->sortBy('date')->values();

        // Inventory Movement Report Data
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $inventoryQuery = \App\Models\Product::query()
            ->select([
                'product.ProductID',
                'product.ProductName',
                'product.ProductDescription',
                \DB::raw('COALESCE(SUM(pi.ProductQuantity),0) as total_in'),
                \DB::raw('COALESCE(SUM(od.OrderQuantity),0) as total_out'),
                \DB::raw('COALESCE(SUM(pi.ProductQuantity),0) - COALESCE(SUM(od.OrderQuantity),0) as current_stock')
            ])
            ->leftJoin('productinventory as pi', function($join) use ($startDate, $endDate) {
                $join->on('product.ProductID', '=', 'pi.ProductID');
                if ($startDate) $join->where('pi.created_at', '>=', $startDate);
                if ($endDate) $join->where('pi.created_at', '<=', $endDate);
            })
            ->leftJoin('orderdetails as od', function($join) use ($startDate, $endDate) {
                $join->on('product.ProductID', '=', 'od.ProductID');
                // For order details, we'll filter by order date through orders table
                $join->join('orders', 'od.OrderID', '=', 'orders.OrderID');
                if ($startDate) $join->where('orders.OrderDate', '>=', $startDate);
                if ($endDate) $join->where('orders.OrderDate', '<=', $endDate);
            })
            ->groupBy('product.ProductID', 'product.ProductName', 'product.ProductDescription');

        $products = $inventoryQuery->get();

        return view('admin.reports.index', compact(
            'filterType',
            'month',
            'year',
            'dateFrom',
            'dateTo',
            'orders',
            'customerStats',
            'topCustomers',
            'summary',
            'dailySales',
            'products',
            'startDate',
            'endDate'
        ));
    }
}
