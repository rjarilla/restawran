<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OrderDetails;
use App\Models\Orders;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Orders::with(['customer', 'orderDetails.product', 'payment'])
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
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'averageOrderValue' => $averageOrderValue,
            'itemsSold' => $itemsSold,
            'latestOrderDate' => $latestOrderDate,
        ]);
    }

    public function create()
    {
        return view('admin.orders.create', [
            'products' => $this->getProductsWithAvailability(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = $this->validateOrderRequest($request, true, true);

        if ($validator->fails()) {
            return redirect()->route('admin.orders.create')->withErrors($validator)->withInput();
        }

        $requestedItems = $this->requestedItems($request);

        try {
            DB::transaction(function () use ($request, $requestedItems) {
                $products = $this->getProductsWithAvailability($requestedItems->keys()->all())->keyBy('ProductID');
                $lineItems = $this->buildLineItems($requestedItems, $products);

                $customerCode = $this->nextCustomerCode();
                $customer = Customer::create($this->customerData([
                    'CustomerCode' => $customerCode,
                    'CustomerName' => $request->input('CustomerName'),
                    'CustomerAddressLine1' => $request->input('CustomerAddressLine1'),
                    'CustomerAddressLine2' => $request->input('CustomerAddressLine2', ''),
                    'CustomerStreet' => $request->input('CustomerStreet', ''),
                    'CustomerCity' => $request->input('CustomerCity', ''),
                    'CustomerProvince' => $request->input('CustomerProvince', ''),
                    'CustomerPostalCode' => $request->input('CustomerPostalCode', ''),
                    'CustomerEmail' => $request->input('CustomerEmail', ''),
                    'CustomerContactNumber' => $request->input('CustomerContactNumber', ''),
                    'CustomerUpdateBy' => session('user_name', 'admin'),
                    'CustomerUpdateDate' => now(),
                ]));
                $customer = $this->resolveCustomerRecord($customer, $customerCode);

                $order = Orders::create($this->orderData([
                    'OrderID' => (string) Str::uuid(),
                    'OrderDate' => now()->toDateString(),
                    'CustomerID' => $customer->CustomerID,
                    'OrderTotalAmount' => round($lineItems->sum('line_total'), 2),
                    'OrderFulfilledBy' => session('user_name', 'admin'),
                ]));

                foreach ($lineItems as $lineItem) {
                    $this->consumeInventory($lineItem['product']->ProductID, $lineItem['quantity']);
                    $this->saveOrderDetail($order->OrderID, $lineItem);
                }

                Payment::create($this->paymentData([
                    'PaymentID' => (string) Str::uuid(),
                    'OrderID' => $order->OrderID,
                    'PaymentMode' => $request->input('PaymentMode'),
                    'PaymentTotal' => round($lineItems->sum('line_total'), 2),
                    'PaymentChange' => 0,
                    'PaymentStatus' => 'resolved',
                ]));
            });
        } catch (RuntimeException $exception) {
            return redirect()->route('admin.orders.create')
                ->withErrors(['items' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()->route('admin.orders.index')->with('success', 'Order added successfully.');
    }

    public function edit(string $id)
    {
        $order = Orders::with(['customer', 'orderDetails.product', 'payment'])->findOrFail($id);

        return view('admin.orders.edit', [
            'order' => $order,
            'products' => $this->getProductsWithAvailability(),
        ]);
    }

    public function show(string $id)
    {
        return redirect()->route('admin.orders.edit', $id);
    }

    public function update(Request $request, string $id)
    {
        $order = Orders::with(['orderDetails', 'payment'])->findOrFail($id);
        $validator = $this->validateOrderRequest($request, false, (bool) $request->boolean('resolve_payment'));

        if ($validator->fails()) {
            return redirect()->route('admin.orders.edit', $order->OrderID)->withErrors($validator)->withInput();
        }

        $requestedItems = $this->requestedItems($request);
        $currentItems = $order->orderDetails->keyBy('ProductID');

        try {
            DB::transaction(function () use ($request, $order, $requestedItems, $currentItems) {
                $products = $this->getProductsWithAvailability($requestedItems->keys()->all())->keyBy('ProductID');

                foreach ($requestedItems as $productId => $newQuantity) {
                    $currentQuantity = (int) optional($currentItems->get($productId))->OrderQuantity;
                    $product = $products->get($productId);

                    if (!$product) {
                        throw new RuntimeException('One or more selected products are no longer available.');
                    }

                    $availableForOrder = $product->available_quantity + $currentQuantity;
                    if ($newQuantity > $availableForOrder) {
                        throw new RuntimeException("{$product->ProductName} only has {$availableForOrder} item(s) available for this order.");
                    }

                    if ($newQuantity > $currentQuantity) {
                        $this->consumeInventory($productId, $newQuantity - $currentQuantity);
                    } elseif ($newQuantity < $currentQuantity) {
                        $this->restoreInventory($productId, $currentQuantity - $newQuantity);
                    }
                }

                foreach ($currentItems as $productId => $detail) {
                    if (!$requestedItems->has($productId)) {
                        $this->restoreInventory($productId, (int) $detail->OrderQuantity);
                    }
                }

                $order->orderDetails()->delete();
                $lineItems = $this->buildLineItems($requestedItems, $products);

                foreach ($lineItems as $lineItem) {
                    $this->saveOrderDetail($order->OrderID, $lineItem);
                }

                $total = round($lineItems->sum('line_total'), 2);
                $order->OrderTotalAmount = $total;
                $order->save();

                if ($order->payment) {
                    $order->payment->PaymentTotal = $total;

                    if ($request->filled('PaymentMode')) {
                        $order->payment->PaymentMode = $request->input('PaymentMode');
                    }

                    $order->payment->save();
                }

                if ($request->boolean('mark_fulfilled') && $order->OrderFulfilledBy === 'PENDING') {
                    $order->OrderFulfilledBy = session('user_name', 'admin');
                    $order->save();
                }
            });
        } catch (RuntimeException $exception) {
            return redirect()->route('admin.orders.edit', $order->OrderID)
                ->withErrors(['items' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()->route('admin.orders.index')->with('success', 'Order updated successfully.');
    }

    public function destroy(string $id)
    {
        $order = Orders::with(['orderDetails', 'payment'])->findOrFail($id);

        DB::transaction(function () use ($order) {
            foreach ($order->orderDetails as $detail) {
                $this->restoreInventory($detail->ProductID, (int) $detail->OrderQuantity);
            }

            $order->payment?->delete();
            $order->orderDetails()->delete();
            $order->delete();
        });

        return redirect()->route('admin.orders.index')->with('success', 'Order removed successfully.');
    }

    private function validateOrderRequest(Request $request, bool $includeCustomer = true, bool $requirePaymentMode = false)
    {
        $rules = [
            'items' => 'required|array',
            'items.*' => 'nullable|integer|min:0',
        ];

        if ($requirePaymentMode) {
            $rules['PaymentMode'] = 'required|in:cash,gcash,credit_card,bank_transfer,cash_on_delivery';
        } else {
            $rules['PaymentMode'] = 'nullable|in:cash,gcash,credit_card,bank_transfer,cash_on_delivery';
            $rules['resolve_payment'] = 'nullable|boolean';
        }

        if ($includeCustomer) {
            $rules = array_merge([
                'CustomerName' => 'required|string|max:200',
                'CustomerEmail' => 'nullable|email|max:200',
                'CustomerContactNumber' => 'nullable|string|max:200',
                'CustomerAddressLine1' => 'nullable|string|max:200',
                'CustomerAddressLine2' => 'nullable|string|max:200',
                'CustomerStreet' => 'nullable|string|max:200',
                'CustomerCity' => 'nullable|string|max:200',
                'CustomerProvince' => 'nullable|string|max:200',
                'CustomerPostalCode' => 'nullable|string|max:200',
            ], $rules);
        }

        $validator = Validator::make($request->all(), $rules);
        $validator->after(function ($validator) use ($request) {
            if ($this->requestedItems($request)->isEmpty()) {
                $validator->errors()->add('items', 'Select at least one item quantity.');
            }
        });

        return $validator;
    }

    private function paymentData(array $data): array
    {
        return $this->filterTableColumns('payments', $data);
    }

    private function requestedItems(Request $request): Collection
    {
        return collect($request->input('items', []))
            ->mapWithKeys(fn ($quantity, $productId) => [$productId => (int) $quantity])
            ->filter(fn ($quantity) => $quantity > 0);
    }

    private function buildLineItems(Collection $requestedItems, Collection $products): Collection
    {
        return $requestedItems->map(function (int $quantity, $productId) use ($products) {
            $product = $products->get($productId);

            if (!$product) {
                throw new RuntimeException('One or more selected products are no longer available.');
            }

            $unitPrice = $this->resolveProductPrice($product);

            return [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => round($quantity * $unitPrice, 2),
            ];
        })->values();
    }

    private function saveOrderDetail(string $orderId, array $lineItem): void
    {
        OrderDetails::create($this->orderDetailData([
            'OrderDetailsID' => (string) Str::uuid(),
            'OrderID' => $orderId,
            'ProductID' => $lineItem['product']->ProductID,
            'OrderQuantity' => $lineItem['quantity'],
            'OrderQuantityPrice' => $lineItem['unit_price'],
            'OrderItemTotal' => $lineItem['line_total'],
        ]));
    }

    private function getProductsWithAvailability(array $productIds = []): Collection
    {
        $today = now()->toDateString();
        $productColumns = Schema::getColumnListing('product');
        $inventoryColumns = Schema::getColumnListing('productinventory');
        $usesProductRemaining = in_array('ProductQuantityRemaining', $productColumns, true);
        $usesInventoryRemaining = in_array('ProductQuantityRemaining', $inventoryColumns, true);

        return Product::query()
            ->when($productIds !== [], fn ($query) => $query->whereIn('ProductID', $productIds))
            ->with(['inventories' => function ($inventoryQuery) use ($today, $inventoryColumns, $usesInventoryRemaining) {
                    $inventoryQuery
                        ->where($usesInventoryRemaining ? 'ProductQuantityRemaining' : 'ProductQuantity', '>', 0)
                        ->when(in_array('ProductBatchDeliveryDate', $inventoryColumns, true), function ($dateQuery) use ($today) {
                            $dateQuery->where(function ($deliveryQuery) use ($today) {
                                $deliveryQuery->whereNull('ProductBatchDeliveryDate')
                                    ->orWhereDate('ProductBatchDeliveryDate', '<=', $today);
                            });
                        })
                        ->when(in_array('ProductBatchExpiry', $inventoryColumns, true), function ($dateQuery) use ($today) {
                            $dateQuery->whereDate('ProductBatchExpiry', '>=', $today);
                        })
                        ->when(in_array('ProductBatchExpiry', $inventoryColumns, true), fn ($orderQuery) => $orderQuery->orderBy('ProductBatchExpiry'))
                        ->when(in_array('ProductBatchDeliveryDate', $inventoryColumns, true), fn ($orderQuery) => $orderQuery->orderBy('ProductBatchDeliveryDate'));
                }])
            ->orderBy('ProductName')
            ->get()
            ->map(function (Product $product) use ($usesProductRemaining, $usesInventoryRemaining) {
                $product->available_quantity = $this->resolveAvailableQuantity(
                    $product,
                    $usesProductRemaining,
                    $usesInventoryRemaining
                );
                $product->is_available = $product->available_quantity > 0;
                $product->display_price = $this->resolveProductPrice($product);

                return $product;
            })
            ->values();
    }

    private function consumeInventory($productId, int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        $remaining = $quantity;
        $today = now()->toDateString();
        $inventoryColumns = Schema::getColumnListing('productinventory');
        $usesInventoryRemaining = in_array('ProductQuantityRemaining', $inventoryColumns, true);

        $batches = ProductInventory::query()
            ->where('ProductID', $productId)
            ->where($usesInventoryRemaining ? 'ProductQuantityRemaining' : 'ProductQuantity', '>', 0)
            ->when(in_array('ProductBatchDeliveryDate', $inventoryColumns, true), function ($query) use ($today) {
                $query->where(function ($deliveryQuery) use ($today) {
                    $deliveryQuery->whereNull('ProductBatchDeliveryDate')
                        ->orWhereDate('ProductBatchDeliveryDate', '<=', $today);
                });
            })
            ->when(in_array('ProductBatchExpiry', $inventoryColumns, true), function ($query) use ($today) {
                $query->whereDate('ProductBatchExpiry', '>=', $today);
            })
            ->when(in_array('ProductBatchExpiry', $inventoryColumns, true), fn ($query) => $query->orderBy('ProductBatchExpiry'))
            ->when(in_array('ProductBatchDeliveryDate', $inventoryColumns, true), fn ($query) => $query->orderBy('ProductBatchDeliveryDate'))
            ->lockForUpdate()
            ->get();

        if ($batches->isEmpty() && Schema::hasColumn('product', 'ProductQuantityRemaining')) {
            $product = Product::query()->where('ProductID', $productId)->lockForUpdate()->first();
            $available = $product ? max(0, (int) $product->ProductQuantityRemaining) : 0;

            if (!$product || $available < $quantity) {
                throw new RuntimeException('Not enough inventory remains to complete the order.');
            }

            $product->ProductQuantityRemaining = $available - $quantity;
            $product->save();

            return;
        }

        foreach ($batches as $batch) {
            $availableInBatch = (int) ($usesInventoryRemaining ? $batch->ProductQuantityRemaining : $batch->ProductQuantity);
            $deducted = min($availableInBatch, $remaining);
            $this->updateInventoryBatchQuantity($batch, $availableInBatch - $deducted);
            $remaining -= $deducted;

            if ($remaining <= 0) {
                return;
            }
        }

        throw new RuntimeException('Not enough inventory remains to complete the order.');
    }

    private function restoreInventory($productId, int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        $inventoryColumns = Schema::getColumnListing('productinventory');
        $usesInventoryRemaining = in_array('ProductQuantityRemaining', $inventoryColumns, true);

        $batch = ProductInventory::query()
            ->where('ProductID', $productId)
            ->when($usesInventoryRemaining, fn ($query) => $query->orderByDesc('ProductQuantityRemaining'))
            ->orderByDesc('ProductQuantity')
            ->lockForUpdate()
            ->first();

        if ($batch) {
            $currentQuantity = (int) ($usesInventoryRemaining ? $batch->ProductQuantityRemaining : $batch->ProductQuantity);
            $this->updateInventoryBatchQuantity($batch, $currentQuantity + $quantity);
            return;
        }

        if (Schema::hasColumn('product', 'ProductQuantityRemaining')) {
            Product::query()
                ->where('ProductID', $productId)
                ->lockForUpdate()
                ->increment('ProductQuantityRemaining', $quantity);

            return;
        }

        ProductInventory::create([
            'ProductBatchID' => (string) Str::uuid(),
            'ProductID' => $productId,
            'ProductQuantity' => $quantity,
            'ProductQuantityRemaining' => $quantity,
            'ProductBatchDeliveryDate' => now()->toDateString(),
            'ProductReceivedBy' => session('user_name', 'admin'),
        ]);
    }

    private function updateInventoryBatchQuantity(ProductInventory $batch, int $quantity): void
    {
        $query = DB::table('productinventory');

        if (Schema::hasColumn('productinventory', 'ProductInventoryID') && $batch->ProductInventoryID) {
            $query->where('ProductInventoryID', $batch->ProductInventoryID);
        } else {
            $query->where('ProductBatchID', $batch->ProductBatchID);
        }

        $payload = Schema::hasColumn('productinventory', 'ProductQuantityRemaining')
            ? ['ProductQuantityRemaining' => $quantity]
            : ['ProductQuantity' => $quantity];

        $query->update($payload);
    }

    private function resolveAvailableQuantity(Product $product, bool $usesProductRemaining, bool $usesInventoryRemaining): int
    {
        $inventoryAvailable = (int) $product->inventories
            ->sum(fn (ProductInventory $inventory) => max(0, (int) ($usesInventoryRemaining
                ? $inventory->ProductQuantityRemaining
                : $inventory->ProductQuantity)));

        if ($inventoryAvailable > 0 || !$usesProductRemaining) {
            return $inventoryAvailable;
        }

        return max(0, (int) $product->ProductQuantityRemaining);
    }

    private function resolveProductPrice(Product $product): float
    {
        $today = now()->toDateString();
        $salePrice = $product->ProductPriceSale ?? null;
        $discountStarts = $product->ProductDiscountStartDate ?? null;
        $discountEnds = $product->ProductDiscountEndDate ?? null;
        $withinWindow = (!$discountStarts || $discountStarts <= $today)
            && (!$discountEnds || $discountEnds >= $today);
        $isDiscounted = (int) ($product->ProductOnDiscount ?? 0) === 1 && $salePrice !== null && $withinWindow;

        return (float) ($isDiscounted ? $salePrice : $product->ProductPrice);
    }

    private function nextCustomerCode(): string
    {
        $lastCustomer = DB::table($this->customerTable())
            ->whereNotNull('CustomerCode')
            ->orderByDesc('CustomerID')
            ->first();

        $nextNumber = $lastCustomer && preg_match('/(\d+)$/', (string) $lastCustomer->CustomerCode, $matches)
            ? ((int) $matches[1]) + 1
            : 1;

        return 'CUST-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function customerData(array $data): array
    {
        return $this->filterTableColumns($this->customerTable(), $data);
    }

    private function orderData(array $data): array
    {
        return $this->filterTableColumns('orders', $data);
    }

    private function orderDetailData(array $data): array
    {
        return $this->filterTableColumns('orderdetails', $data);
    }

    private function filterTableColumns(string $table, array $data): array
    {
        $columns = Schema::getColumnListing($table);

        return collect($data)
            ->filter(fn ($value, $column) => in_array($column, $columns, true))
            ->all();
    }

    private function resolveCustomerRecord(Customer $customer, string $customerCode): Customer
    {
        if (!empty($customer->CustomerID)) {
            return $customer;
        }

        $reloadedCustomer = Customer::query()
            ->where('CustomerCode', $customerCode)
            ->orderByDesc('CustomerID')
            ->first();

        if (!$reloadedCustomer || empty($reloadedCustomer->CustomerID)) {
            throw new RuntimeException('Customer record was created without an ID.');
        }

        return $reloadedCustomer;
    }

    private function customerTable(): string
    {
        return Schema::hasTable('customer') ? 'customer' : 'customers';
    }
}
