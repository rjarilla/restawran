<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'week');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $searchPaymentId = $request->input('search_payment_id');
        $searchOrderId = $request->input('search_order_id');
        $paymentMode = $request->input('payment_mode');

        $query = Payment::with(['order.customer', 'order.orderDetails.product'])
            ->whereHas('order.orderDetails')
            ->orderBy('PaymentID', 'desc');

        // PaymentID search
        if ($searchPaymentId) {
            $query->where('PaymentID', 'LIKE', '%' . $searchPaymentId . '%');
        }

        // OrderID search
        if ($searchOrderId) {
            $query->where('OrderID', 'LIKE', '%' . $searchOrderId . '%');
        }

        // Payment mode filter
        if ($paymentMode) {
            $query->where('PaymentMode', $paymentMode);
        }

        if ($period) {
            $query->whereHas('order', function ($q) use ($period) {
                $today = now();
                switch ($period) {
                    case 'today':
                        $q->whereDate('OrderDate', $today->toDateString());
                        break;
                    case 'yesterday':
                        $q->whereDate('OrderDate', $today->copy()->subDay()->toDateString());
                        break;
                    case 'week':
                        $q->whereBetween('OrderDate', [
                            $today->copy()->startOfWeek()->toDateString(),
                            $today->copy()->endOfWeek()->toDateString()
                        ]);
                        break;
                    case 'last_7_days':
                        $q->whereBetween('OrderDate', [
                            $today->copy()->subDays(6)->toDateString(),
                            $today->toDateString()
                        ]);
                        break;
                    case 'month':
                        $q->whereMonth('OrderDate', $today->month)
                          ->whereYear('OrderDate', $today->year);
                        break;
                    case 'last_30_days':
                        $q->whereBetween('OrderDate', [
                            $today->copy()->subDays(29)->toDateString(),
                            $today->toDateString()
                        ]);
                        break;
                }
            });
        } elseif ($dateFrom || $dateTo) {
            $query->whereHas('order', function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom && $dateTo) {
                    $q->whereBetween('OrderDate', [$dateFrom, $dateTo]);
                } elseif ($dateFrom) {
                    $q->whereDate('OrderDate', '>=', $dateFrom);
                } elseif ($dateTo) {
                    $q->whereDate('OrderDate', '<=', $dateTo);
                }
            });
        }

        $payments = $query->get();

        $totalAmountPaid = $payments
            ->filter(fn ($payment) => ($payment->PaymentStatus ?? 'resolved') === 'resolved')
            ->sum('PaymentTotal');
        $totalSoldItems = 0;
        $uniqueCustomers = [];

        foreach ($payments as $payment) {
            if ($payment->order && $payment->order->orderDetails) {
                foreach ($payment->order->orderDetails as $orderDetail) {
                    $totalSoldItems += $orderDetail->OrderQuantity;
                }
            }

            if ($payment->order && $payment->order->customer) {
                $uniqueCustomers[] = $payment->order->customer->CustomerID;
            }
        }

        $totalCustomers = count(array_unique($uniqueCustomers));

        return view('admin.payments.index', compact(
            'payments',
            'totalAmountPaid',
            'totalSoldItems',
            'totalCustomers',
            'period',
            'dateFrom',
            'dateTo',
            'searchPaymentId',
            'searchOrderId',
            'paymentMode'
        ));
    }

    public function show($id)
    {
        $payment = Payment::with(['order.customer', 'order.orderDetails.product'])
            ->where('PaymentID', $id)
            ->firstOrFail();

        return view('admin.payments.show', compact('payment'));
    }

    public function destroy($id)
    {
        $payment = Payment::where('PaymentID', $id)->firstOrFail();
        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
}
