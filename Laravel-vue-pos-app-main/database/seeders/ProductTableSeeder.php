<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = ['pcs', 'kg', 'ltr', 'box'];

        $productNames = ['Wireless Bluetooth Speaker', 'Organic Basmati Rice', 'LED Desk Lamp with USB', 'Pack of Cotton T-Shirts', 'Premium Olive Oil', 'Multivitamin Capsules', 'Portable Power Bank 20000mAh', 'Ceramic Dinner Plate Set', 'Running Shoes for Men', 'Wireless Earbuds with Case', 'Natural Honey 500g', 'Digital Kitchen Scale', 'Notebook with Leather Cover', 'Electric Kettle 1.5L', 'Instant Coffee Jar', 'Smartphone Tripod Stand', 'Handcrafted Wooden Bowl', 'USB-C Fast Charging Cable', 'Laundry Detergent Liquid', 'Face Moisturizer Cream', 'Pack of Ballpoint Pens', 'Table Fan 16 inch', 'Scented Soy Candles', 'Rechargeable LED Flashlight', 'Wall Hanging Planters', 'Non-stick Frying Pan', 'Fitness Resistance Bands Set', 'Ceramic Mug Set', 'Travel Toiletry Bag', 'Digital Alarm Clock'];

        foreach ($productNames as $index => $name) {
            Product::create([
                'name' => $name,
                'brand_id' => rand(1, 17),
                'category_id' => rand(1, 15),
                'user_id' => 1,
                'description' => $this->generateDescription($name),
                'price' => rand(100, 1000),
                'unit' => $units[array_rand($units)],
                'stock' => rand(10, 100),
                'image' => null,
            ]);
        }
    }

    private function generateDescription(string $name): string
    {
        $lorem = "Introducing the {$name}, a reliable and thoughtfully crafted product designed to meet your everyday needs with style and functionality. Whether you're upgrading your home, improving your health, or simply making life more convenient, this item brings exceptional value to your routine. Crafted with high-quality materials, it ensures durability and performance you can count on. Its sleek design blends well with any environment, and its usability makes it suitable for everyone. Enjoy long-lasting satisfaction, backed by excellent build and versatility. Perfect for home, office, or travel. Don’t miss the chance to elevate your lifestyle with the practicality of this amazing product.";

        return $lorem;
    }
}
