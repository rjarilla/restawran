<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orders;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Orders::with(['customer', 'orderDetails'])
            ->orderByDesc('OrderDate')
            ->get();

        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('OrderTotalAmount');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $itemsSold = $orders->sum(function ($order) {
            return $order->orderDetails->sum('OrderQuantity');
        });
        $latestOrderDate = optional($orders->first())->OrderDate;

        return view('admin.orders.index', [
            'orders' => $orders->take(10),
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'averageOrderValue' => $averageOrderValue,
            'itemsSold' => $itemsSold,
            'latestOrderDate' => $latestOrderDate,
        ]);
    }
}
