<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SupplierTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Alpha Traders', 'Global Supplies Ltd', 'Delta Distribution',
            'Everest Imports', 'Prime Solutions', 'Skyline Traders',
            'Blue Ocean Suppliers', 'Galaxy Enterprise', 'EcoTech Distributors',
            'Zenith Resources', 'Smart Trade Ltd', 'RapidSource',
            'Trusted Vendors', 'UrbanMart', 'Sunrise Traders',
            'Nova Suppliers', 'Fusion Enterprises', 'Coreline Distributions',
            'Silverline Trade', 'MaxValue Supplies'
        ];

        foreach ($names as $index => $name) {
            Supplier::create([
                'name'     => $name,
                'email'    => ($index % 2 == 0) ? strtolower(str_replace(' ', '', $name)) . '@example.com' : null,
                'phone'    => '01810000' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'address'  => 'Address of ' . $name,
                'brand_id' => rand(21, 37),
                'user_id'  => 1,
            ]);
        }
    }
}
