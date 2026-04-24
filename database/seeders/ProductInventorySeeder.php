<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductInventorySeeder extends Seeder
{
    public function run(): void
    {
        $today = now();

        $inventoryBatches = [
            [
                'ProductBatchID' => 'batch-a111-0001-0001-0001-000000000001',
                'ProductID' => 'a1111111-1111-1111-1111-111111111111',
                'ProductQuantity' => 100,
                'ProductBatchDeliveryDate' => $today->copy()->subDays(2)->toDateString(),
                'ProductBatchExpiry' => $today->copy()->addDays(10)->toDateString(),
                'ProductReceivedBy' => 'seed',
            ],
            [
                'ProductBatchID' => 'batch-b222-0002-0002-0002-000000000002',
                'ProductID' => 'b2222222-2222-2222-2222-222222222222',
                'ProductQuantity' => 2,
                'ProductBatchDeliveryDate' => $today->copy()->subDay()->toDateString(),
                'ProductBatchExpiry' => $today->copy()->addDays(3)->toDateString(),
                'ProductReceivedBy' => 'seed',
            ],
            [
                'ProductBatchID' => 'batch-c333-0003-0003-0003-000000000003',
                'ProductID' => 'c3333333-3333-3333-3333-333333333333',
                'ProductQuantity' => 0,
                'ProductBatchDeliveryDate' => $today->copy()->subDay()->toDateString(),
                'ProductBatchExpiry' => $today->copy()->addDays(7)->toDateString(),
                'ProductReceivedBy' => 'seed',
            ],
            [
                'ProductBatchID' => 'batch-d444-0004-0004-0004-000000000004',
                'ProductID' => 'd4444444-4444-4444-4444-444444444444',
                'ProductQuantity' => 25,
                'ProductBatchDeliveryDate' => $today->copy()->subDays(5)->toDateString(),
                'ProductBatchExpiry' => $today->copy()->subDay()->toDateString(),
                'ProductReceivedBy' => 'seed',
            ],
        ];

        DB::table('ProductInventory')->whereIn('ProductID', array_column($inventoryBatches, 'ProductID'))->delete();
        DB::table('ProductInventory')->insert($inventoryBatches);
    }
}
