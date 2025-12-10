<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks (tanpa transaction)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $faker = \Faker\Factory::create();
        
        $categories = [
            // ===================== ELECTRONICS =====================
            [
                'name' => 'Laptops & Notebooks',
                'description' => 'Laptop, notebook, ultrabook, gaming laptop, 2-in-1 convertibles',
                'image_path' => $faker->imageUrl(640, 480, 'laptop', true, 'Laptops'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Desktop Computers',
                'description' => 'Desktop PC, all-in-one computers, workstations, gaming desktops',
                'image_path' => $faker->imageUrl(640, 480, 'computer', true, 'Desktop PC'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Computer Components',
                'description' => 'CPU, motherboard, RAM, GPU, power supply, cooling systems',
                'image_path' => $faker->imageUrl(640, 480, 'technology', true, 'Components'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Computer Peripherals',
                'description' => 'Keyboard, mouse, monitor, webcam, speakers, headphones',
                'image_path' => $faker->imageUrl(640, 480, 'electronics', true, 'Peripherals'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Storage Devices',
                'description' => 'HDD, SSD, USB flash drives, memory cards, external drives',
                'image_path' => $faker->imageUrl(640, 480, 'hardware', true, 'Storage'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Networking Equipment',
                'description' => 'Routers, switches, modems, access points, network cables',
                'image_path' => $faker->imageUrl(640, 480, 'network', true, 'Networking'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== OFFICE EQUIPMENT =====================
            [
                'name' => 'Printers & Scanners',
                'description' => 'Inkjet printers, laser printers, multifunction printers, scanners',
                'image_path' => $faker->imageUrl(640, 480, 'office', true, 'Printers'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Paper, ink cartridges, toners, stationery, office furniture',
                'image_path' => $faker->imageUrl(640, 480, 'business', true, 'Office'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Projectors & Displays',
                'description' => 'Projectors, interactive whiteboards, digital signage, monitors',
                'image_path' => $faker->imageUrl(640, 480, 'presentation', true, 'Projectors'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== MOBILE DEVICES =====================
            [
                'name' => 'Smartphones',
                'description' => 'Android phones, iPhones, tablets, phablets',
                'image_path' => $faker->imageUrl(640, 480, 'phone', true, 'Smartphones'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tablets',
                'description' => 'iPads, Android tablets, Windows tablets, e-readers',
                'image_path' => $faker->imageUrl(640, 480, 'tablet', true, 'Tablets'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mobile Accessories',
                'description' => 'Phone cases, screen protectors, chargers, power banks, cables',
                'image_path' => $faker->imageUrl(640, 480, 'accessories', true, 'Mobile Accessories'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== CONSUMER ELECTRONICS =====================
            [
                'name' => 'Audio Equipment',
                'description' => 'Speakers, headphones, earphones, soundbars, audio systems',
                'image_path' => $faker->imageUrl(640, 480, 'music', true, 'Audio'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gaming Consoles',
                'description' => 'PlayStation, Xbox, Nintendo Switch, gaming accessories',
                'image_path' => $faker->imageUrl(640, 480, 'games', true, 'Gaming'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Smart Home Devices',
                'description' => 'Smart lights, security cameras, smart plugs, voice assistants',
                'image_path' => $faker->imageUrl(640, 480, 'home', true, 'Smart Home'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== SOFTWARE & SERVICES =====================
            [
                'name' => 'Software',
                'description' => 'Operating systems, office suites, antivirus, creative software',
                'image_path' => $faker->imageUrl(640, 480, 'software', true, 'Software'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cloud Services',
                'description' => 'Cloud storage, SaaS, IaaS, PaaS, backup solutions',
                'image_path' => $faker->imageUrl(640, 480, 'cloud', true, 'Cloud'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Access Control Systems',
                'description' => 'Security systems, CCTV, access cards, biometric devices',
                'image_path' => $faker->imageUrl(640, 480, 'security', true, 'Security'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== CABLE & CONNECTIVITY =====================
            [
                'name' => 'Cables & Connectors',
                'description' => 'HDMI, USB, Ethernet, power cables, adapters, connectors',
                'image_path' => $faker->imageUrl(640, 480, 'cables', true, 'Cables'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Power & UPS',
                'description' => 'UPS systems, power strips, surge protectors, batteries',
                'image_path' => $faker->imageUrl(640, 480, 'power', true, 'Power Supply'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Servers & Data Center',
                'description' => 'Server racks, NAS, SAN, server hardware, data center equipment',
                'image_path' => $faker->imageUrl(640, 480, 'server', true, 'Servers'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert semua kategori sekaligus
        DB::table('categories')->insert($categories);

        $this->command->info('Categories seeded successfully!');
        $this->command->info('Total: ' . count($categories) . ' categories created.');
        
        // Show some sample categories
        $this->command->line('');
        $this->command->info('=== SAMPLE CATEGORIES ===');
        $sampleCategories = Category::inRandomOrder()->limit(5)->get();
        foreach ($sampleCategories as $category) {
            $this->command->info("• {$category->name}: {$category->description}");
            $this->command->line("  Image URL: {$category->image_path}");
        }
    }
}