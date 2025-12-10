<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $categories = [
            [
                'name' => 'Laptops & Notebooks',
                'description' => 'Laptop, notebook, ultrabook, gaming laptop, 2-in-1 convertibles',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Desktop Computers',
                'description' => 'Desktop PC, all-in-one computers, workstations, gaming desktops',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Computer Components',
                'description' => 'CPU, motherboard, RAM, GPU, power supply, cooling systems',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Computer Peripherals',
                'description' => 'Keyboard, mouse, monitor, webcam, speakers, headphones',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Storage Devices',
                'description' => 'HDD, SSD, USB flash drives, memory cards, external drives',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Networking Equipment',
                'description' => 'Routers, switches, modems, access points, network cables',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Printers & Scanners',
                'description' => 'Inkjet printers, laser printers, multifunction printers, scanners',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Paper, ink cartridges, toners, stationery, office furniture',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Projectors & Displays',
                'description' => 'Projectors, interactive whiteboards, digital signage, monitors',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Smartphones',
                'description' => 'Android phones, iPhones, tablets, phablets',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tablets',
                'description' => 'iPads, Android tablets, Windows tablets, e-readers',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mobile Accessories',
                'description' => 'Phone cases, screen protectors, chargers, power banks, cables',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Audio Equipment',
                'description' => 'Speakers, headphones, earphones, soundbars, audio systems',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gaming Consoles',
                'description' => 'PlayStation, Xbox, Nintendo Switch, gaming accessories',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Smart Home Devices',
                'description' => 'Smart lights, security cameras, smart plugs, voice assistants',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Software',
                'description' => 'Operating systems, office suites, antivirus, creative software',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cloud Services',
                'description' => 'Cloud storage, SaaS, IaaS, PaaS, backup solutions',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Access Control Systems',
                'description' => 'Security systems, CCTV, access cards, biometric devices',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cables & Connectors',
                'description' => 'HDMI, USB, Ethernet, power cables, adapters, connectors',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Power & UPS',
                'description' => 'UPS systems, power strips, surge protectors, batteries',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Servers & Data Center',
                'description' => 'Server racks, NAS, SAN, server hardware, data center equipment',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categories')->insert($categories);

        $this->command->info('Categories seeded successfully!');
        $this->command->info('Total: ' . count($categories) . ' categories created.');
        
        $sampleCategories = Category::inRandomOrder()->limit(5)->get();
        $this->command->info('Sample categories:');
        foreach ($sampleCategories as $category) {
            $this->command->info("• {$category->name}");
        }
    }
}