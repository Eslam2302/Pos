<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $categories = ['cat one', 'cat two'];

        foreach ($categories as $category) {

            Category::create([

                'en' => ['name' => $category],
                'ar' => ['name' => $category]

            ]);

        }

    }
}
