<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = ["Cairo", "Alexandria", "Giza"];

        foreach ($areas as $area) {
            DB::table('areas')->updateOrInsert(
                ['area' => $area], // Check if area already exists
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
