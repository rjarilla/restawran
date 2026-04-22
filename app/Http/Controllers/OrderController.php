<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\OrderDetails;
use App\Models\Orders;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function create()
    {
        $products = Product::query()
            ->where(function ($query) {
                $query->whereNull('ProductStatus')
                    ->orWhere('ProductStatus', 'Active')
                    ->orWhere('ProductStatus', 'ACTIVE');
            })
            ->orderBy('ProductName')
            ->get()
            ->map(function (Product $product) {
                $product->display_price = $this->resolveProductPrice($product);

                return $product;
            });

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
            'items.*' => 'nullable|numeric|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {
            $items = collect($request->input('items', []))
                ->map(fn ($quantity) => (float) $quantity)
                ->filter(fn ($quantity) => $quantity > 0);

            if ($items->isEmpty()) {
                $validator->errors()->add('items', 'Select at least one item before placing an order.');
            }
        });

        if ($validator->fails()) {
            return redirect()->route('order.create')->withErrors($validator)->withInput();
        }

        $requestedItems = collect($request->input('items', []))
            ->mapWithKeys(fn ($quantity, $productId) => [$productId => (float) $quantity])
            ->filter(fn ($quantity) => $quantity > 0);

        $products = Product::query()
            ->whereIn('ProductID', $requestedItems->keys())
            ->get()
            ->keyBy('ProductID');

        if ($products->count() !== $requestedItems->count()) {
            return redirect()->route('order.create')
                ->withErrors(['items' => 'One or more selected products are no longer available.'])
                ->withInput();
        }

        $lineItems = $requestedItems->map(function (float $quantity, string $productId) use ($products) {
            $product = $products->get($productId);
            $unitPrice = $this->resolveProductPrice($product);

            return [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => round($quantity * $unitPrice, 2),
            ];
        })->values();

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

    private function resolveProductPrice(Product $product): float
    {
        $salePrice = $product->ProductPriceSale;
        $isDiscounted = (int) $product->ProductOnDiscount === 1 && $salePrice !== null;

        return (float) ($isDiscounted ? $salePrice : $product->ProductPrice);
    }
}
