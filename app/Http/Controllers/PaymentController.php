<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\OrderDetails;
use App\Models\Orders;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductInventory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentController extends Controller
{
    public function showPaymentMethod()
    {
        // Get the pending order from session
        $pendingOrder = session('pending_order');
        
        if (!$pendingOrder) {
            return redirect()->route('order.create')
                ->withErrors(['order' => 'No pending order found. Please place an order first.']);
        }

        return view('payment.method', [
            'pendingOrder' => $pendingOrder,
        ]);
    }

    public function processPayment(Request $request)
    {
        // Get the pending order from session
        $pendingOrder = session('pending_order');
        
        if (!$pendingOrder) {
            return redirect()->route('order.create')
                ->withErrors(['order' => 'No pending order found. Please place an order first.']);
        }

        // Validate payment method selection
        $request->validate([
            'payment_method' => 'required|in:gcash,credit_card,bank_transfer,cash_on_delivery',
        ]);

        // Convert session items back to lineItems format for database processing
        $lineItems = collect();
        foreach ($pendingOrder['items'] as $item) {
            // Get the product to ensure it still exists and is available
            $product = Product::find($item['product_id']);
            
            if (!$product) {
                return redirect()->route('order.create')
                    ->withErrors(['items' => 'One or more selected products are no longer available.'])
                    ->withInput();
            }

            $lineItems->push([
                'product' => $product,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $item['line_total'],
            ]);
        }

        try {
            $order = DB::transaction(function () use ($pendingOrder, $lineItems, $request) {
                // Create customer
                $customer = Customer::create([
                    'CustomerCode' => $this->nextCustomerCode(),
                    'CustomerName' => $pendingOrder['customer_details']['CustomerName'],
                    'CustomerAddressLine1' => $pendingOrder['customer_details']['CustomerAddressLine1'],
                    'CustomerAddressLine2' => $pendingOrder['customer_details']['CustomerAddressLine2'],
                    'CustomerStreet' => $pendingOrder['customer_details']['CustomerStreet'],
                    'CustomerCity' => $pendingOrder['customer_details']['CustomerCity'],
                    'CustomerProvince' => $pendingOrder['customer_details']['CustomerProvince'],
                    'CustomerPostalCode' => $pendingOrder['customer_details']['CustomerPostalCode'],
                    'CustomerEmail' => $pendingOrder['customer_details']['CustomerEmail'],
                    'CustomerContactNumber' => $pendingOrder['customer_details']['CustomerContactNumber'],
                    'CustomerUpdateBy' => 'web-order',
                    'CustomerUpdateDate' => now()->toDateString(),
                ]);

                // Create order
                $order = Orders::create([
                    'OrderID' => (string) Str::uuid(),
                    'OrderDate' => now()->toDateString(),
                    'CustomerID' => $customer->CustomerID,
                    'OrderTotalAmount' => round($lineItems->sum('line_total'), 2),
                    'OrderFulfilledBy' => 'PENDING',
                ]);

                // Create order details and consume inventory
                foreach ($lineItems as $lineItem) {
                    $this->consumeInventory($lineItem['product']->ProductID, $lineItem['quantity']);

                    OrderDetails::create([
                        'OrderDetailsID' => (string) Str::uuid(),
                        'OrderID' => $order->OrderID,
                        'ProductID' => $lineItem['product']->ProductID,
                        'OrderQuantity' => $lineItem['quantity'],
                        'OrderQuantityPrice' => $lineItem['unit_price'],
                        'OrderItemTotal' => $lineItem['line_total'],
                    ]);
                }

                // Create payment record
                Payment::create([
                    'PaymentID' => (string) Str::uuid(),
                    'OrderID' => $order->OrderID,
                    'PaymentMode' => $request->payment_method,
                    'PaymentTotal' => round($lineItems->sum('line_total'), 2),
                    'PaymentChange' => 0, // Can be updated later for cash payments
                ]);

                return $order->load(['customer', 'orderDetails.product']);
            });
        } catch (QueryException $exception) {
            report($exception);

            return redirect()->route('order.create')
                ->withErrors(['items' => 'We could not save the order. Please try again.'])
                ->withInput();
        } catch (RuntimeException $exception) {
            session()->forget('pending_order');

            return redirect()->route('order.create')
                ->withErrors(['items' => 'Stock changed while you were processing payment. Please review the available items and try again.'])
                ->withInput();
        }

        // Clear the pending order from session
        session()->forget('pending_order');

        return redirect()->route('order.create')->with('order_confirmation', [
            'order_id' => $order->OrderID,
            'customer_name' => $order->customer->CustomerName,
            'total_amount' => number_format((float) $order->OrderTotalAmount, 2),
            'items' => $order->orderDetails->map(function (OrderDetails $detail) {
                return [
                    'name' => $detail->product?->ProductName ?? $detail->ProductID,
                    'quantity' => $detail->OrderQuantity + 0,
                    'line_total' => number_format((float) $detail->OrderItemTotal, 2),
                ];
            })->all(),
        ]);
    }

    private function consumeInventory(string $productId, int $quantity): void
    {
        if (Schema::hasColumn('product', 'ProductQuantityRemaining')) {
            $product = Product::query()
                ->where('ProductID', $productId)
                ->lockForUpdate()
                ->first();

            if (!$product) {
                throw new RuntimeException('Product is no longer available.');
            }

            $available = max(0, (int) $product->ProductQuantityRemaining);

            if ($available < $quantity) {
                throw new RuntimeException('Not enough inventory remains to complete the order.');
            }

            $product->ProductQuantityRemaining = $available - $quantity;
            $product->save();

            return;
        }

        $remaining = $quantity;
        $today = now()->toDateString();

        $batches = ProductInventory::query()
            ->where('ProductID', $productId)
            ->where(function ($query) use ($today) {
                $query->whereNull('ProductBatchDeliveryDate')
                    ->orWhereDate('ProductBatchDeliveryDate', '<=', $today);
            })
            ->whereDate('ProductBatchExpiry', '>=', $today)
            ->where('ProductQuantity', '>', 0)
            ->orderBy('ProductBatchExpiry')
            ->orderBy('ProductBatchDeliveryDate')
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $availableInBatch = (int) $batch->ProductQuantity;
            $deducted = min($availableInBatch, $remaining);

            if ($deducted <= 0) {
                continue;
            }

            $this->updateInventoryBatchQuantity($batch, $availableInBatch - $deducted);

            $remaining -= $deducted;
        }

        if ($remaining > 0) {
            throw new RuntimeException('Not enough inventory remains to complete the order.');
        }
    }

    private function nextCustomerCode(): string
    {
        $lastCustomer = Customer::query()
            ->whereNotNull('CustomerCode')
            ->orderByDesc('CustomerID')
            ->first();

        $nextNumber = $lastCustomer && preg_match('/(\d+)$/', (string) $lastCustomer->CustomerCode, $matches)
            ? ((int) $matches[1]) + 1
            : 1;

        return 'CUST-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function updateInventoryBatchQuantity(ProductInventory $batch, int $quantity): void
    {
        $query = DB::table('productinventory');

        if (Schema::hasColumn('productinventory', 'ProductInventoryID') && $batch->ProductInventoryID) {
            $query->where('ProductInventoryID', $batch->ProductInventoryID);
        } elseif ($batch->ProductBatchID) {
            $query->where('ProductBatchID', $batch->ProductBatchID);
        } else {
            throw new RuntimeException('Inventory batch cannot be updated.');
        }

        $updated = $query->update(['ProductQuantity' => $quantity]);

        if ($updated === 0) {
            throw new RuntimeException('Inventory batch changed while processing.');
        }
    }
}
