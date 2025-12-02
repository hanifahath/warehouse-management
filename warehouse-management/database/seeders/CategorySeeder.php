<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Electronics', 'Furniture', 'Food', 'Clothing'];

        foreach ($categories as $c) {
            Category::firstOrCreate(['name' => $c]);
        }
    }
}
