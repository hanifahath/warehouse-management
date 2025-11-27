<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        Warehouse::create(['name' => 'Main Warehouse', 'location' => 'Jakarta']);
        Warehouse::create(['name' => 'Secondary Warehouse', 'location' => 'Bandung']);
    }
}
