<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Ahsanullah',
            'last_name'  => 'Ariful',
            'email'      => 'ariful@example.com',
            'role'      => '1',
            'mobile'     => '01700000001',
            'address'    => 'Dhaka, Bangladesh',
            'password'   => Hash::make('password123'),
        ]);

        User::create([
            'first_name' => 'Sadhin',
            'last_name'  => 'Sharkar',
            'email'      => 'sadhin@example.com',
            'role'      => '2',
            'mobile'     => '01700000002',
            'address'    => 'Chittagong, Bangladesh',
            'password'   => Hash::make('password456'),
        ]);
    }
}
