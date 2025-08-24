<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Electronics',
            'Home Appliances',
            'Men\'s Fashion',
            'Women\'s Fashion',
            'Books & Stationery',
            'Beauty & Personal Care',
            'Sports & Outdoors',
            'Toys & Games',
            'Grocery & Essentials',
            'Furniture',
            'Health & Wellness',
            'Mobile & Accessories',
            'Computers & Laptops',
            'Automotive',
            'Pet Supplies'
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'name'        => $categoryName,
                'description' => $categoryName . ' category description Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
                'image' => null,
                'user_id'     => 1,
            ]);
        }
    }
}
