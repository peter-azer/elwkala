<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductsPacksSizes;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $products = ProductsPacksSizes::all();

        foreach($products as $product){
            $product->update([
                'quantity' => 1000,
            ]);
        }
    }
}
