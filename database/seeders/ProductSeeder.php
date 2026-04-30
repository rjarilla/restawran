<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $today = now()->toDateString();

        $products = [
            [
                'ProductID' => 'a1111111-1111-1111-1111-111111111111',
                'ProductName' => 'Garlic Butter Steak Bowl',
                'ProductDescription' => 'Sliced steak over butter rice with roasted vegetables.',
                'ProductCategoryID' => 'RICE',
                'ProductQuantityTypeID' => 'BOWL',
                'ProductImagePath' => 'img/menu-1.jpg',
                'ProductPrice' => 289.00,
                'ProductOnDiscount' => 0,
                'ProductPriceSale' => null,
                'ProductDiscountStartDate' => null,
                'ProductDiscountEndDate' => null,
                'ProductStatus' => 'Active',
                'ProductUpdatedBy' => 'seed',
                'ProductUpdatedDate' => $today,
            ],
            [
                'ProductID' => 'b2222222-2222-2222-2222-222222222222',
                'ProductName' => 'Truffle Mushroom Pasta',
                'ProductDescription' => 'Creamy truffle pasta with sauteed mushrooms and parmesan.',
                'ProductCategoryID' => 'PASTA',
                'ProductQuantityTypeID' => 'PLATE',
                'ProductImagePath' => 'img/menu-2.jpg',
                'ProductPrice' => 245.00,
                'ProductOnDiscount' => 1,
                'ProductPriceSale' => 219.00,
                'ProductDiscountStartDate' => $today,
                'ProductDiscountEndDate' => now()->addDays(5)->toDateString(),
                'ProductStatus' => 'Active',
                'ProductUpdatedBy' => 'seed',
                'ProductUpdatedDate' => $today,
            ],
            [
                'ProductID' => 'c3333333-3333-3333-3333-333333333333',
                'ProductName' => 'Smoked Chicken Wrap',
                'ProductDescription' => 'Grilled chicken wrap with crisp greens and chipotle mayo.',
                'ProductCategoryID' => 'WRAPS',
                'ProductQuantityTypeID' => 'PIECE',
                'ProductImagePath' => 'img/menu-3.jpg',
                'ProductPrice' => 159.00,
                'ProductOnDiscount' => 0,
                'ProductPriceSale' => null,
                'ProductDiscountStartDate' => null,
                'ProductDiscountEndDate' => null,
                'ProductStatus' => 'Active',
                'ProductUpdatedBy' => 'seed',
                'ProductUpdatedDate' => $today,
            ],
            [
                'ProductID' => 'd4444444-4444-4444-4444-444444444444',
                'ProductName' => 'Citrus Iced Tea',
                'ProductDescription' => 'Fresh brewed iced tea finished with a bright citrus blend.',
                'ProductCategoryID' => 'DRINKS',
                'ProductQuantityTypeID' => 'CUP',
                'ProductImagePath' => 'img/menu-4.jpg',
                'ProductPrice' => 89.00,
                'ProductOnDiscount' => 0,
                'ProductPriceSale' => null,
                'ProductDiscountStartDate' => null,
                'ProductDiscountEndDate' => null,
                'ProductStatus' => 'Active',
                'ProductUpdatedBy' => 'seed',
                'ProductUpdatedDate' => $today,
            ],
        ];

        DB::table('ProductInventory')->whereIn('ProductID', array_column($products, 'ProductID'))->delete();
        DB::table('Product')->whereIn('ProductID', array_column($products, 'ProductID'))->delete();
        DB::table('Product')->insert($products);
    }
}
