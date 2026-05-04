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

    public function productSales(Request $request)
    {
        $salesMonth = $request->input('sales_month', now()->month);
        $salesYear  = $request->input('sales_year',  now()->year);

        $salesProducts = \DB::table('orderdetails as od')
            ->join('orders as o',        'od.OrderID',          '=', 'o.OrderID')
            ->join('product as p',       'od.ProductID',        '=', 'p.ProductID')
            ->leftJoin('lookup as cat',  'p.ProductCategoryID', '=', 'cat.LookupID')
            ->whereMonth('o.OrderDate', $salesMonth)
            ->whereYear('o.OrderDate',  $salesYear)
            ->select(
                'p.ProductID',
                'p.ProductName',
                'p.ProductDescription',
                'p.ProductPrice',
                \DB::raw('COALESCE(cat.LookupValue, cat.LookupName, p.ProductCategoryID) as ProductCategory'),
                \DB::raw('SUM(od.OrderQuantity)                   as total_qty'),
                \DB::raw('SUM(od.OrderQuantity * p.ProductPrice)  as total_revenue')
            )
            ->groupBy(
                'p.ProductID',
                'p.ProductName',
                'p.ProductDescription',
                'p.ProductPrice',
                'p.ProductCategoryID',
                'cat.LookupValue',
                'cat.LookupName'
            )
            ->orderByDesc('total_revenue')
            ->get();

        $salesSummary = [
            'totalRevenue' => $salesProducts->sum('total_revenue'),
            'totalUnits'   => $salesProducts->sum('total_qty'),
            'productCount' => $salesProducts->count(),
            'bestSeller'   => $salesProducts->first()?->ProductName ?? '—',
        ];

        $salesByCategory = $salesProducts
            ->groupBy('ProductCategory')
            ->map(fn($items) => $items->sum('total_revenue'))
            ->sortByDesc(fn($v) => $v);

        return view('admin.reports.product_sales', compact(
            'salesProducts',
            'salesSummary',
            'salesByCategory',
            'salesMonth',
            'salesYear'
        ));
    }

    public function monthlySales(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $export = $request->input('export');

        // Get monthly orders with payments and order details
        $monthlyOrders = Orders::with(['customer', 'payment', 'orderDetails.product'])
            ->whereHas('payment')
            ->has('orderDetails')
            ->whereMonth('OrderDate', $month)
            ->whereYear('OrderDate', $year)
            ->orderByDesc('OrderDate')
            ->get();

        // Calculate monthly revenue
        $monthlyRevenue = $monthlyOrders->sum(function ($order) {
            return $order->payment ? $order->payment->PaymentTotal : $order->OrderTotalAmount;
        });

        // Get daily sales breakdown
        $dailySales = $monthlyOrders->groupBy('OrderDate')->map(function ($dayOrders, $date) {
            $dailyRevenue = $dayOrders->sum(function ($order) {
                return $order->payment ? $order->payment->PaymentTotal : $order->OrderTotalAmount;
            });
            $dailyOrders = $dayOrders->count();
            
            return [
                'date' => $date,
                'revenue' => $dailyRevenue,
                'orders' => $dailyOrders,
                'average_order_value' => $dailyOrders > 0 ? $dailyRevenue / $dailyOrders : 0,
            ];
        })->sortBy('date')->values();

        // Get top selling products for the month
        $topProducts = \DB::table('orderdetails as od')
            ->join('orders as o', 'od.OrderID', '=', 'o.OrderID')
            ->join('product as p', 'od.ProductID', '=', 'p.ProductID')
            ->whereMonth('o.OrderDate', $month)
            ->whereYear('o.OrderDate', $year)
            ->select(
                'p.ProductID',
                'p.ProductName',
                \DB::raw('SUM(od.OrderQuantity) as total_quantity'),
                \DB::raw('SUM(od.OrderQuantity * p.ProductPrice) as total_revenue'),
                \DB::raw('COUNT(DISTINCT od.OrderID) as unique_orders')
            )
            ->groupBy('p.ProductID', 'p.ProductName')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Get payment mode breakdown
        $paymentModes = $monthlyOrders->groupBy(function ($order) {
            return $order->payment ? $order->payment->PaymentMode : 'unknown';
        })->map(function ($orders, $mode) {
            $revenue = $orders->sum(function ($order) {
                return $order->payment ? $order->payment->PaymentTotal : $order->OrderTotalAmount;
            });
            return [
                'mode' => $mode,
                'count' => $orders->count(),
                'revenue' => $revenue,
                'percentage' => 0, // Will be calculated below
            ];
        })->sortByDesc('revenue');

        // Calculate percentages for payment modes
        $totalRevenueForPercentage = $paymentModes->sum('revenue');
        if ($totalRevenueForPercentage > 0) {
            $paymentModes = $paymentModes->map(function ($mode) use ($totalRevenueForPercentage) {
                $mode['percentage'] = ($mode['revenue'] / $totalRevenueForPercentage) * 100;
                return $mode;
            });
        }

        // Get customer statistics
        $customerStats = [
            'total_customers' => $monthlyOrders->pluck('CustomerID')->unique()->count(),
            'new_customers' => 0, // Would need customer creation date for this
            'repeat_customers' => $monthlyOrders->groupBy('CustomerID')->filter(function ($orders) {
                return $orders->count() > 1;
            })->count(),
            'average_orders_per_customer' => $monthlyOrders->count() > 0 ? 
                $monthlyOrders->count() / $monthlyOrders->pluck('CustomerID')->unique()->count() : 0,
        ];

        // Monthly summary
        $monthlySummary = [
            'total_orders' => $monthlyOrders->count(),
            'total_revenue' => $monthlyRevenue,
            'average_order_value' => $monthlyOrders->count() > 0 ? $monthlyRevenue / $monthlyOrders->count() : 0,
            'total_customers' => $customerStats['total_customers'],
            'repeat_customers' => $customerStats['repeat_customers'],
            'best_day' => $dailySales->max('revenue') ? 
                $dailySales->firstWhere('revenue', $dailySales->max('revenue')) : null,
            'best_product' => $topProducts->first(),
        ];

        // Handle PDF export
        if ($export === 'pdf') {
            // You can implement PDF generation here using DomPDF or similar library
            // For now, return a simple view that can be printed to PDF
            return view('admin.reports.monthly_payment_pdf', compact(
                'month',
                'year',
                'monthlyOrders',
                'monthlyRevenue',
                'dailySales',
                'topProducts',
                'paymentModes',
                'customerStats',
                'monthlySummary'
            ));
        }

        return view('admin.reports.monthly_payment', compact(
            'month',
            'year',
            'monthlyOrders',
            'monthlyRevenue',
            'dailySales',
            'topProducts',
            'paymentModes',
            'customerStats',
            'monthlySummary'
        ));
    }

}
