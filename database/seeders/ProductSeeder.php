<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'ProductName' => 'Garlic Butter Steak Bowl',
                'ProductDescription' => 'Sliced steak over butter rice with roasted vegetables.',
                'ProductPrice' => 289.00,
            ],
            [
                'ProductName' => 'Truffle Mushroom Pasta',
                'ProductDescription' => 'Creamy truffle pasta with sauteed mushrooms and parmesan.',
                'ProductPrice' => 245.00,
            ],
            [
                'ProductName' => 'Smoked Chicken Wrap',
                'ProductDescription' => 'Grilled chicken wrap with crisp greens and chipotle mayo.',
                'ProductPrice' => 159.00,
            ],
            [
                'ProductName' => 'Citrus Iced Tea',
                'ProductDescription' => 'Fresh brewed iced tea finished with a bright citrus blend.',
                'ProductPrice' => 89.00,
            ],
        ];

        DB::table('product')->insert($products);
    }
}
