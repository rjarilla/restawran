<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $today = now()->toDateString();

        DB::table('Product')->upsert([
            [
                'ProductID' => '11111111-1111-1111-1111-111111111111',
                'ProductName' => 'Classic Chicken Burger',
                'ProductDescription' => 'Crispy chicken fillet with lettuce and signature sauce.',
                'ProductCategoryID' => 'BURGERS',
                'ProductQuantityTypeID' => 'PLATE',
                'ProductImagePath' => 'img/menu-1.jpg',
                'ProductPrice' => 189.00,
                'ProductOnDiscount' => 0,
                'ProductPriceSale' => null,
                'ProductDiscountStartDate' => null,
                'ProductDiscountEndDate' => null,
                'ProductStatus' => 'Active',
                'ProductUpdatedBy' => 'seed',
                'ProductUpdatedDate' => $today,
            ],
            [
                'ProductID' => '22222222-2222-2222-2222-222222222222',
                'ProductName' => 'Creamy Carbonara Pasta',
                'ProductDescription' => 'Creamy pasta with bacon bits and parmesan cheese.',
                'ProductCategoryID' => 'PASTA',
                'ProductQuantityTypeID' => 'BOWL',
                'ProductImagePath' => 'img/menu-2.jpg',
                'ProductPrice' => 229.00,
                'ProductOnDiscount' => 1,
                'ProductPriceSale' => 199.00,
                'ProductDiscountStartDate' => $today,
                'ProductDiscountEndDate' => $today,
                'ProductStatus' => 'Active',
                'ProductUpdatedBy' => 'seed',
                'ProductUpdatedDate' => $today,
            ],
            [
                'ProductID' => '33333333-3333-3333-3333-333333333333',
                'ProductName' => 'Pepperoni Pizza Slice',
                'ProductDescription' => 'Loaded pepperoni pizza slice with mozzarella and herbs.',
                'ProductCategoryID' => 'PIZZA',
                'ProductQuantityTypeID' => 'SLICE',
                'ProductImagePath' => 'img/menu-3.jpg',
                'ProductPrice' => 149.00,
                'ProductOnDiscount' => 0,
                'ProductPriceSale' => null,
                'ProductDiscountStartDate' => null,
                'ProductDiscountEndDate' => null,
                'ProductStatus' => 'Active',
                'ProductUpdatedBy' => 'seed',
                'ProductUpdatedDate' => $today,
            ],
            [
                'ProductID' => '44444444-4444-4444-4444-444444444444',
                'ProductName' => 'Loaded Fries',
                'ProductDescription' => 'Seasoned fries topped with cheese sauce and bacon.',
                'ProductCategoryID' => 'SIDES',
                'ProductQuantityTypeID' => 'BOX',
                'ProductImagePath' => 'img/menu-4.jpg',
                'ProductPrice' => 129.00,
                'ProductOnDiscount' => 0,
                'ProductPriceSale' => null,
                'ProductDiscountStartDate' => null,
                'ProductDiscountEndDate' => null,
                'ProductStatus' => 'Active',
                'ProductUpdatedBy' => 'seed',
                'ProductUpdatedDate' => $today,
            ],
            [
                'ProductID' => '55555555-5555-5555-5555-555555555555',
                'ProductName' => 'Iced Coffee',
                'ProductDescription' => 'Cold brew coffee served over ice with fresh milk.',
                'ProductCategoryID' => 'DRINKS',
                'ProductQuantityTypeID' => 'CUP',
                'ProductImagePath' => 'img/menu-5.jpg',
                'ProductPrice' => 99.00,
                'ProductOnDiscount' => 0,
                'ProductPriceSale' => null,
                'ProductDiscountStartDate' => null,
                'ProductDiscountEndDate' => null,
                'ProductStatus' => 'Active',
                'ProductUpdatedBy' => 'seed',
                'ProductUpdatedDate' => $today,
            ],
        ], ['ProductID'], [
            'ProductName',
            'ProductDescription',
            'ProductCategoryID',
            'ProductQuantityTypeID',
            'ProductImagePath',
            'ProductPrice',
            'ProductOnDiscount',
            'ProductPriceSale',
            'ProductDiscountStartDate',
            'ProductDiscountEndDate',
            'ProductStatus',
            'ProductUpdatedBy',
            'ProductUpdatedDate',
        ]);
    }
}
