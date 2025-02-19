<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Market;

class MarketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $users = [
            [
                'name' => 'mohammed',
                'phone' => '1234567890',
                'password' => Hash::make('password123'), // Always hash passwords
            ],
            [
                'name' => 'ahmed',
                'phone' => '0987654321',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'samar',
                'phone' => '1122334455',
                'password' => Hash::make('password123'),
            ]
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['phone' => $user['phone']], $user);
        }

        $user = User::first(); // Assign the first user to the markets

        $markets = [
            [
                'user_id' => $user->id ?? 2,
                'phone2' => '2223334444',
                'phone3' => '3334445555',
                'owner_name' => 'mohammed Owner',
                'manager_name' => 'mohammed Manager',
                'market_name' => 'SuperMart',
                'address' => '123 Market Street',
                'max_order_quantity' => 7,
                'area_id' => 1,
            ],
            [
                'user_id' => $user->id ?? 2,
                'phone2' => '5556667777',
                'phone3' => '6667778888',
                'owner_name' => 'ahmed Owner',
                'manager_name' => 'ahmed Manager',
                'market_name' => 'MegaStore',
                'address' => '456 Commerce Ave',
                'max_order_quantity' => 8,
                'area_id' => 2,
            ]
        ];

        foreach ($markets as $market) {
            Market::updateOrCreate(['market_name' => $market['market_name']], $market);
        }
    }
}
