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
                'ProductID' => 1,
                'ProductQuantity' => 100,
                'ProductBatchExpiry' => $today->copy()->addDays(10)->toDateString(),
            ],
            [
                'ProductID' => 2,
                'ProductQuantity' => 2,
                'ProductBatchExpiry' => $today->copy()->addDays(3)->toDateString(),
            ],
            [
                'ProductID' => 3,
                'ProductQuantity' => 0,
                'ProductBatchExpiry' => $today->copy()->addDays(7)->toDateString(),
            ],
            [
                'ProductID' => 4,
                'ProductQuantity' => 25,
                'ProductBatchExpiry' => $today->copy()->subDay()->toDateString(),
            ],
        ];

        DB::table('productinventory')->insert($inventoryBatches);
    }
}
