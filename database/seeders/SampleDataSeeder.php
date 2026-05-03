<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Orders;
use App\Models\Payment;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample customers
        $customers = [
            [
                'CustomerCode' => 'CUST-0001',
                'CustomerName' => 'John Doe',
                'CustomerEmail' => 'john@example.com',
                'CustomerContactNumber' => '1234567890',
                'CustomerAddressLine1' => '123 Main St',
                'CustomerCity' => 'Sample City',
                'CustomerProvince' => 'Sample Province',
                'CustomerUpdateDate' => now()
            ],
            [
                'CustomerCode' => 'CUST-0002',
                'CustomerName' => 'Jane Smith',
                'CustomerEmail' => 'jane@example.com',
                'CustomerContactNumber' => '0987654321',
                'CustomerAddressLine1' => '456 Oak Ave',
                'CustomerCity' => 'Another City',
                'CustomerProvince' => 'Another Province',
                'CustomerUpdateDate' => now()
            ]
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        // Create sample orders and payments
        $sampleOrders = [
            [
                'customer_code' => 'CUST-0001',
                'order_date' => now()->toDateString(),
                'total' => 150.00,
                'payment_mode' => 'Cash'
            ],
            [
                'customer_code' => 'CUST-0002',
                'order_date' => now()->subDays(5)->toDateString(),
                'total' => 200.00,
                'payment_mode' => 'Card'
            ],
            [
                'customer_code' => 'CUST-0001',
                'order_date' => now()->subDays(10)->toDateString(),
                'total' => 75.50,
                'payment_mode' => 'Cash'
            ]
        ];

        foreach ($sampleOrders as $orderData) {
            $customer = Customer::where('CustomerCode', $orderData['customer_code'])->first();

            $order = Orders::create([
                'OrderID' => (string) Str::uuid(),
                'OrderDate' => $orderData['order_date'],
                'CustomerID' => $customer->CustomerID,
                'OrderTotalAmount' => $orderData['total'],
                'OrderFulfilledBy' => 'admin'
            ]);

            Payment::create([
                'PaymentID' => (string) Str::uuid(),
                'OrderID' => $order->OrderID,
                'PaymentMode' => $orderData['payment_mode'],
                'PaymentTotal' => $orderData['total'],
                'PaymentChange' => 0
            ]);
        }

        // Create sample products using raw DB inserts to match actual schema
        $product1Id = \Illuminate\Support\Facades\DB::table('product')->insertGetId([
            'ProductName' => 'Single Item Product',
            'ProductDescription' => 'A product with only 1 item in stock.',
            'ProductPrice' => 10.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        \Illuminate\Support\Facades\DB::table('productinventory')->insert([
            'ProductID' => $product1Id,
            'ProductQuantity' => 1,
            'ProductBatchExpiry' => now()->addMonths(6),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $product2Id = \Illuminate\Support\Facades\DB::table('product')->insertGetId([
            'ProductName' => 'Bulk Product',
            'ProductDescription' => 'A product with 20 items in stock.',
            'ProductPrice' => 50.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\DB::table('productinventory')->insert([
            'ProductID' => $product2Id,
            'ProductQuantity' => 20,
            'ProductBatchExpiry' => now()->addMonths(6),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
