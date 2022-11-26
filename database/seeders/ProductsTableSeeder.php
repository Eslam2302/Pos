<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $products = ['pro one', 'pro two'];

        foreach ($products as $product) {

            Product::create([

                'category_id' => 1,
                'en' => ['name' => $product, 'description' => $product . ' desc'],
                'ar' => ['name' => $product, 'description' => $product . ' desc'],
                'purchase_price' => 100,
                'sale_price' => 135,
                'stock' => 35

            ]);

        }

    }
}
