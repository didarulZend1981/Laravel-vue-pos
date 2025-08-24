<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BrandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $brands = [
            'Samsung',
            'Apple',
            'Nike',
            'Adidas',
            'Sony',
            'LG',
            'Dell',
            'HP',
            'Lenovo',
            'Panasonic',
            'Puma',
            'Asus',
            'Philips',
            'Reebok',
            'Xiaomi',
            'Walton',
            'Hatil',
        ];

        foreach ($brands as $brandName) {
            Brand::create([
                'name'        => $brandName,
                'description' => $brandName . ' brand description It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
                'user_id'     => 1,
                'image' => null,
            ]);
        }
    }
}
