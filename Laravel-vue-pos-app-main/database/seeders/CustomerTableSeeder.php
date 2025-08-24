<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'John Doe', 'Jane Smith', 'Michael Johnson', 'Emily Davis',
            'William Brown', 'Olivia Wilson', 'James Taylor', 'Sophia Martinez',
            'Benjamin Anderson', 'Thomas', 'Elijah Jackson', 'Mike White',
            'Lucas Harris', 'Charlotte Martin', 'Mason Thompson', 'Amelia Garcia',
            'Ethan Lewis', 'Harper Clark', 'Logan Young', 'Abigail Hall'
        ];

        foreach ($names as $index => $name) {
            Customer::create([
                'user_id' => 1,
                'name'    => $name,
                'email'   => 'customer' . ($index + 1) . '@example.com',
                'phone'   => '01710000' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'zip'     => rand(1000, 9999),
                'address' => 'Address for ' . $name,
                'comment' => 'Regular customer',
            ]);
        }
    }
}
