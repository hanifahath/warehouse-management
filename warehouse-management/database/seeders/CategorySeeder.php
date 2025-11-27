<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Electronics', 'Furniture', 'Food', 'Clothing'];

        foreach ($categories as $c) {
            Category::create(['name' => $c]);
        }
    }
}
