<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\OrderDetails;
use App\Models\Orders;
use App\Models\Product;
use App\Models\ProductInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;

class OrderController extends Controller
{
    public function create()
    {
        $products = $this->getOrderableProducts();

        return view('order', [
            'products' => $products,
            'confirmation' => session('order_confirmation'),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'CustomerName' => 'required|string|max:200',
            'CustomerAddressLine1' => 'required|string|max:200',
            'CustomerAddressLine2' => 'nullable|string|max:200',
            'CustomerStreet' => 'required|string|max:200',
            'CustomerCity' => 'required|string|max:200',
            'CustomerProvince' => 'required|string|max:200',
            'CustomerPostalCode' => 'required|string|max:200',
            'CustomerEmail' => 'required|email|max:200',
            'CustomerContactNumber' => 'required|string|max:200',
            'items' => 'required|array',
            'items.*' => 'nullable|integer|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {
            $items = collect($request->input('items', []))
                ->map(fn ($quantity) => (int) $quantity)
                ->filter(fn ($quantity) => $quantity > 0);

            if ($items->isEmpty()) {
                $validator->errors()->add('items', 'Select at least one available item before placing an order.');
            }
        });

        if ($validator->fails()) {
            return redirect()->route('order.create')->withErrors($validator)->withInput();
        }

        $requestedItems = collect($request->input('items', []))
            ->mapWithKeys(fn ($quantity, $productId) => [$productId => (int) $quantity])
            ->filter(fn ($quantity) => $quantity > 0);

        $products = $this->getOrderableProducts($requestedItems->keys()->all())->keyBy('ProductID');

        if ($products->count() !== $requestedItems->count()) {
            return redirect()->route('order.create')
                ->withErrors(['items' => 'One or more selected products are no longer available.'])
                ->withInput();
        }

        $lineItems = collect();

        foreach ($requestedItems as $productId => $quantity) {
            /** @var \App\Models\Product|null $product */
            $product = $products->get($productId);

            if (!$product) {
                return redirect()->route('order.create')
                    ->withErrors(['items' => 'One or more selected products are no longer available.'])
                    ->withInput();
            }

            if (!$product->is_available) {
                return redirect()->route('order.create')
                    ->withErrors(['items' => "{$product->ProductName} is not available for ordering right now."])
                    ->withInput();
            }

            if ($quantity > $product->available_quantity) {
                return redirect()->route('order.create')
                    ->withErrors([
                        'items' => "{$product->ProductName} only has {$product->available_quantity} item(s) remaining.",
                    ])
                    ->withInput();
            }

            $unitPrice = $this->resolveProductPrice($product);

            $lineItems->push([
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => round($quantity * $unitPrice, 2),
            ]);
        }

        // Database save functionality commented out - redirecting to payment method instead
        /*
        try {
            $order = DB::transaction(function () use ($request, $lineItems) {
                $customer = Customer::create([
                    'CustomerID' => (string) Str::uuid(),
                    'CustomerName' => $request->input('CustomerName'),
                    'CustomerAddressLine1' => $request->input('CustomerAddressLine1'),
                    'CustomerAddressLine2' => $request->input('CustomerAddressLine2', ''),
                    'CustomerStreet' => $request->input('CustomerStreet'),
                    'CustomerCity' => $request->input('CustomerCity'),
                    'CustomerProvince' => $request->input('CustomerProvince'),
                    'CustomerPostalCode' => $request->input('CustomerPostalCode'),
                    'CustomerEmail' => $request->input('CustomerEmail'),
                    'CustomerContactNumber' => $request->input('CustomerContactNumber'),
                    'CustomerUpdateBy' => 'web-order',
                    'CustomerUpdateDate' => now()->toDateString(),
                ]);

                $order = Orders::create([
                    'OrderID' => (string) Str::uuid(),
                    'OrderDate' => now()->toDateString(),
                    'CustomerID' => $customer->CustomerID,
                    'OrderTotalAmount' => round($lineItems->sum('line_total'), 2),
                    'OrderFulfilledBy' => 'PENDING',
                ]);

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

                return $order->load(['customer', 'orderDetails.product']);
            });
        } catch (RuntimeException $exception) {
            return redirect()->route('order.create')
                ->withErrors(['items' => 'Stock changed while you were checking out. Please review the available items and try again.'])
                ->withInput();
        }

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
        */

        // Store order data in session for payment processing
        session([
            'pending_order' => [
                'customer_details' => [
                    'CustomerName' => $request->input('CustomerName'),
                    'CustomerAddressLine1' => $request->input('CustomerAddressLine1'),
                    'CustomerAddressLine2' => $request->input('CustomerAddressLine2', ''),
                    'CustomerStreet' => $request->input('CustomerStreet'),
                    'CustomerCity' => $request->input('CustomerCity'),
                    'CustomerProvince' => $request->input('CustomerProvince'),
                    'CustomerPostalCode' => $request->input('CustomerPostalCode'),
                    'CustomerEmail' => $request->input('CustomerEmail'),
                    'CustomerContactNumber' => $request->input('CustomerContactNumber'),
                ],
                'items' => $lineItems->map(function ($lineItem) {
                    return [
                        'product_id' => $lineItem['product']->ProductID,
                        'product_name' => $lineItem['product']->ProductName,
                        'quantity' => $lineItem['quantity'],
                        'unit_price' => $lineItem['unit_price'],
                        'line_total' => $lineItem['line_total'],
                    ];
                })->all(),
                'total_amount' => round($lineItems->sum('line_total'), 2),
            ]
        ]);

        // Redirect to payment method
        return redirect()->route('payment.method');
    }

    private function getOrderableProducts(array $productIds = []): Collection
    {
        $today = now()->toDateString();

        $products = Product::query()
            ->where(function ($query) {
                $query->whereNull('ProductStatus')
                    ->orWhere('ProductStatus', 'Active')
                    ->orWhere('ProductStatus', 'ACTIVE');
            })
            ->when($productIds !== [], fn ($query) => $query->whereIn('ProductID', $productIds))
            ->with(['inventories' => function ($query) use ($today) {
                $query->whereDate('ProductBatchDeliveryDate', '<=', $today)
                    ->whereDate('ProductBatchExpiry', '>=', $today)
                    ->orderBy('ProductBatchExpiry')
                    ->orderBy('ProductBatchDeliveryDate');
            }])
            ->orderBy('ProductName')
            ->get()
            ->map(function (Product $product) {
                $product->available_quantity = (int) $product->inventories
                    ->sum(fn (ProductInventory $inventory) => max(0, (int) $inventory->ProductQuantity));
                $product->is_available = $product->available_quantity > 0;
                $product->display_price = $this->resolveProductPrice($product);

                return $product;
            });

        if ($productIds === []) {
            return $products;
        }

        return collect($productIds)
            ->map(fn ($productId) => $products->firstWhere('ProductID', $productId))
            ->filter();
    }

    private function consumeInventory(string $productId, int $quantity): void
    {
        $remaining = $quantity;
        $today = now()->toDateString();

        $batches = ProductInventory::query()
            ->where('ProductID', $productId)
            ->whereDate('ProductBatchDeliveryDate', '<=', $today)
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

            $batch->ProductQuantity = $availableInBatch - $deducted;
            $batch->save();

            $remaining -= $deducted;
        }

        if ($remaining > 0) {
            throw new RuntimeException('Not enough inventory remains to complete the order.');
        }
    }

    private function resolveProductPrice(Product $product): float
    {
        $today = now()->toDateString();
        $salePrice = $product->ProductPriceSale;
        $discountStarts = $product->ProductDiscountStartDate;
        $discountEnds = $product->ProductDiscountEndDate;
        $withinWindow = (!$discountStarts || $discountStarts <= $today)
            && (!$discountEnds || $discountEnds >= $today);
        $isDiscounted = (int) $product->ProductOnDiscount === 1
            && $salePrice !== null
            && $withinWindow;

        return (float) ($isDiscounted ? $salePrice : $product->ProductPrice);
    }
}

