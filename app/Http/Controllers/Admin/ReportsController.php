<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->input('filter_type', 'month');
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Orders::with(['customer', 'payment'])
            ->whereHas('payment');

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

        return view('admin.reports.index', compact(
            'filterType',
            'month',
            'year',
            'dateFrom',
            'dateTo',
            'orders',
            'customerStats',
            'topCustomers',
            'summary'
        ));
    }
}
