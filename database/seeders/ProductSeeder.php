<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Inisialisasi Faker
        $faker = \Faker\Factory::create();
        
        // Ambil semua kategori sebagai array biasa
        $categories = Category::all();
        
        $products = [
            // ===================== LAPTOPS & NOTEBOOKS =====================
            [
                'sku' => 'LAP-DELL-001',
                'name' => 'Dell XPS 13 Laptop',
                'category_id' => $categories->firstWhere('name', 'Laptops & Notebooks')->id,
                'description' => 'Intel Core i7, 16GB RAM, 512GB SSD, 13.4" FHD+ Touch Display',
                'purchase_price' => 12500000,
                'selling_price' => 14500000,
                'min_stock' => 5,
                'current_stock' => 12,
                'unit' => 'pcs',
                'rack_location' => 'A1-01',
                'image_path' => $faker->imageUrl(640, 480, 'laptop', true, 'Dell XPS'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'LAP-MAC-001',
                'name' => 'Apple MacBook Pro 14"',
                'category_id' => $categories->firstWhere('name', 'Laptops & Notebooks')->id,
                'description' => 'M3 Pro, 16GB RAM, 512GB SSD, 14-inch Liquid Retina XDR',
                'purchase_price' => 28500000,
                'selling_price' => 32500000,
                'min_stock' => 3,
                'current_stock' => 8,
                'unit' => 'pcs',
                'rack_location' => 'A1-02',
                'image_path' => $faker->imageUrl(640, 480, 'laptop', true, 'MacBook Pro'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'LAP-LENOVO-001',
                'name' => 'Lenovo ThinkPad X1 Carbon',
                'category_id' => $categories->firstWhere('name', 'Laptops & Notebooks')->id,
                'description' => 'Intel Core i5, 16GB RAM, 1TB SSD, 14" WUXGA',
                'purchase_price' => 18500000,
                'selling_price' => 21500000,
                'min_stock' => 4,
                'current_stock' => 6,
                'unit' => 'pcs',
                'rack_location' => 'A1-03',
                'image_path' => $faker->imageUrl(640, 480, 'laptop', true, 'ThinkPad'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== DESKTOP COMPUTERS =====================
            [
                'sku' => 'DESK-ASUS-001',
                'name' => 'ASUS Gaming Desktop',
                'category_id' => $categories->firstWhere('name', 'Desktop Computers')->id,
                'description' => 'Intel i7, RTX 4070, 32GB RAM, 1TB SSD, 2TB HDD',
                'purchase_price' => 22500000,
                'selling_price' => 26500000,
                'min_stock' => 3,
                'current_stock' => 5,
                'unit' => 'pcs',
                'rack_location' => 'B1-01',
                'image_path' => $faker->imageUrl(640, 480, 'computer', true, 'Gaming PC'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'DESK-HP-001',
                'name' => 'HP ProDesk 400 G9',
                'category_id' => $categories->firstWhere('name', 'Desktop Computers')->id,
                'description' => 'Intel i5, 8GB RAM, 512GB SSD, Windows 11 Pro',
                'purchase_price' => 8500000,
                'selling_price' => 10500000,
                'min_stock' => 8,
                'current_stock' => 15,
                'unit' => 'pcs',
                'rack_location' => 'B1-02',
                'image_path' => $faker->imageUrl(640, 480, 'computer', true, 'Office Desktop'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== COMPUTER COMPONENTS =====================
            [
                'sku' => 'CPU-INTEL-001',
                'name' => 'Intel Core i9-13900K',
                'category_id' => $categories->firstWhere('name', 'Computer Components')->id,
                'description' => '24 Cores, 32 Threads, 5.8GHz Max Turbo',
                'purchase_price' => 9500000,
                'selling_price' => 11500000,
                'min_stock' => 10,
                'current_stock' => 25,
                'unit' => 'pcs',
                'rack_location' => 'C1-01',
                'image_path' => $faker->imageUrl(640, 480, 'electronics', true, 'CPU Processor'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'RAM-CORSAIR-001',
                'name' => 'Corsair Vengeance RGB 32GB',
                'category_id' => $categories->firstWhere('name', 'Computer Components')->id,
                'description' => 'DDR5 6000MHz, 2x16GB, CL36',
                'purchase_price' => 2800000,
                'selling_price' => 3500000,
                'min_stock' => 15,
                'current_stock' => 40,
                'unit' => 'pcs',
                'rack_location' => 'C1-02',
                'image_path' => $faker->imageUrl(640, 480, 'electronics', true, 'RAM Memory'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'GPU-NVIDIA-001',
                'name' => 'NVIDIA RTX 4080',
                'category_id' => $categories->firstWhere('name', 'Computer Components')->id,
                'description' => '16GB GDDR6X, Founders Edition',
                'purchase_price' => 18500000,
                'selling_price' => 22500000,
                'min_stock' => 5,
                'current_stock' => 8,
                'unit' => 'pcs',
                'rack_location' => 'C1-03',
                'image_path' => $faker->imageUrl(640, 480, 'electronics', true, 'Graphics Card'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== COMPUTER PERIPHERALS =====================
            [
                'sku' => 'MON-LG-001',
                'name' => 'LG UltraGear 27" Monitor',
                'category_id' => $categories->firstWhere('name', 'Computer Peripherals')->id,
                'description' => '27" 4K UHD, 144Hz, IPS, HDR400',
                'purchase_price' => 6500000,
                'selling_price' => 8500000,
                'min_stock' => 8,
                'current_stock' => 18,
                'unit' => 'pcs',
                'rack_location' => 'D1-01',
                'image_path' => $faker->imageUrl(640, 480, 'technology', true, 'Gaming Monitor'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'KEY-LOGI-001',
                'name' => 'Logitech MX Keys Keyboard',
                'category_id' => $categories->firstWhere('name', 'Computer Peripherals')->id,
                'description' => 'Wireless, Backlit, Multi-device',
                'purchase_price' => 1250000,
                'selling_price' => 1650000,
                'min_stock' => 12,
                'current_stock' => 30,
                'unit' => 'pcs',
                'rack_location' => 'D1-02',
                'image_path' => $faker->imageUrl(640, 480, 'keyboard', true, 'Wireless Keyboard'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'MOUSE-RAZER-001',
                'name' => 'Razer DeathAdder V3',
                'category_id' => $categories->firstWhere('name', 'Computer Peripherals')->id,
                'description' => 'Gaming Mouse, 30K DPI, Wireless',
                'purchase_price' => 950000,
                'selling_price' => 1350000,
                'min_stock' => 15,
                'current_stock' => 35,
                'unit' => 'pcs',
                'rack_location' => 'D1-03',
                'image_path' => $faker->imageUrl(640, 480, 'mouse', true, 'Gaming Mouse'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== STORAGE DEVICES =====================
            [
                'sku' => 'SSD-SAMSUNG-001',
                'name' => 'Samsung 980 Pro 2TB',
                'category_id' => $categories->firstWhere('name', 'Storage Devices')->id,
                'description' => 'NVMe M.2 SSD, PCIe 4.0, 7000MB/s Read',
                'purchase_price' => 3200000,
                'selling_price' => 4200000,
                'min_stock' => 20,
                'current_stock' => 50,
                'unit' => 'pcs',
                'rack_location' => 'E1-01',
                'image_path' => $faker->imageUrl(640, 480, 'technology', true, 'SSD Drive'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'HDD-WD-001',
                'name' => 'WD Blue 4TB HDD',
                'category_id' => $categories->firstWhere('name', 'Storage Devices')->id,
                'description' => '3.5" Internal Hard Drive, 5400 RPM',
                'purchase_price' => 1250000,
                'selling_price' => 1650000,
                'min_stock' => 25,
                'current_stock' => 60,
                'unit' => 'pcs',
                'rack_location' => 'E1-02',
                'image_path' => $faker->imageUrl(640, 480, 'hardware', true, 'Hard Drive'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== NETWORKING EQUIPMENT =====================
            [
                'sku' => 'ROUTER-TPLINK-001',
                'name' => 'TP-Link Archer AX73',
                'category_id' => $categories->firstWhere('name', 'Networking Equipment')->id,
                'description' => 'WiFi 6 Router, Dual Band, 5400Mbps',
                'purchase_price' => 1850000,
                'selling_price' => 2450000,
                'min_stock' => 10,
                'current_stock' => 22,
                'unit' => 'pcs',
                'rack_location' => 'F1-01',
                'image_path' => $faker->imageUrl(640, 480, 'network', true, 'WiFi Router'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== PRINTERS & SCANNERS =====================
            [
                'sku' => 'PRINTER-HP-001',
                'name' => 'HP LaserJet Pro M404dn',
                'category_id' => $categories->firstWhere('name', 'Printers & Scanners')->id,
                'description' => 'Monochrome Laser Printer, Duplex, Ethernet',
                'purchase_price' => 4200000,
                'selling_price' => 5500000,
                'min_stock' => 6,
                'current_stock' => 14,
                'unit' => 'pcs',
                'rack_location' => 'G1-01',
                'image_path' => $faker->imageUrl(640, 480, 'office', true, 'Laser Printer'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== SMARTPHONES =====================
            [
                'sku' => 'PHONE-IPHONE-001',
                'name' => 'iPhone 15 Pro 256GB',
                'category_id' => $categories->firstWhere('name', 'Smartphones')->id,
                'description' => '6.1" Super Retina XDR, A17 Pro, 5G',
                'purchase_price' => 18500000,
                'selling_price' => 22500000,
                'min_stock' => 8,
                'current_stock' => 20,
                'unit' => 'pcs',
                'rack_location' => 'H1-01',
                'image_path' => $faker->imageUrl(640, 480, 'phone', true, 'iPhone 15'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'PHONE-SAMSUNG-001',
                'name' => 'Samsung Galaxy S24 Ultra',
                'category_id' => $categories->firstWhere('name', 'Smartphones')->id,
                'description' => '6.8" Dynamic AMOLED, Snapdragon 8 Gen 3, 512GB',
                'purchase_price' => 17500000,
                'selling_price' => 21500000,
                'min_stock' => 8,
                'current_stock' => 18,
                'unit' => 'pcs',
                'rack_location' => 'H1-02',
                'image_path' => $faker->imageUrl(640, 480, 'phone', true, 'Samsung Galaxy'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== AUDIO EQUIPMENT =====================
            [
                'sku' => 'HEADPHONE-SONY-001',
                'name' => 'Sony WH-1000XM5',
                'category_id' => $categories->firstWhere('name', 'Audio Equipment')->id,
                'description' => 'Wireless Noise Cancelling Headphones',
                'purchase_price' => 4800000,
                'selling_price' => 6200000,
                'min_stock' => 12,
                'current_stock' => 28,
                'unit' => 'pcs',
                'rack_location' => 'I1-01',
                'image_path' => $faker->imageUrl(640, 480, 'headphones', true, 'Noise Cancelling'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== CABLES & CONNECTORS =====================
            [
                'sku' => 'CABLE-HDMI-001',
                'name' => 'HDMI 2.1 Cable 2m',
                'category_id' => $categories->firstWhere('name', 'Cables & Connectors')->id,
                'description' => 'High Speed HDMI Cable, 8K 60Hz, 48Gbps',
                'purchase_price' => 85000,
                'selling_price' => 150000,
                'min_stock' => 50,
                'current_stock' => 200,
                'unit' => 'pcs',
                'rack_location' => 'J1-01',
                'image_path' => $faker->imageUrl(640, 480, 'cable', true, 'HDMI Cable'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'CABLE-USB-001',
                'name' => 'USB-C to USB-C Cable 1m',
                'category_id' => $categories->firstWhere('name', 'Cables & Connectors')->id,
                'description' => 'USB 3.2 Gen 2, 10Gbps, 100W Power Delivery',
                'purchase_price' => 75000,
                'selling_price' => 120000,
                'min_stock' => 100,
                'current_stock' => 300,
                'unit' => 'pcs',
                'rack_location' => 'J1-02',
                'image_path' => $faker->imageUrl(640, 480, 'cable', true, 'USB-C Cable'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===================== LOW STOCK ITEMS (FOR TESTING) =====================
            [
                'sku' => 'LOW-STOCK-001',
                'name' => 'Crucial P3 500GB SSD',
                'category_id' => $categories->firstWhere('name', 'Storage Devices')->id,
                'description' => 'NVMe PCIe 3.0, 3500MB/s Read',
                'purchase_price' => 650000,
                'selling_price' => 850000,
                'min_stock' => 10,
                'current_stock' => 3, // LOW STOCK
                'unit' => 'pcs',
                'rack_location' => 'E1-03',
                'image_path' => $faker->imageUrl(640, 480, 'technology', true, 'SSD'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'OUT-STOCK-001',
                'name' => 'Microsoft Surface Pro 9',
                'category_id' => $categories->firstWhere('name', 'Laptops & Notebooks')->id,
                'description' => '13" 2-in-1 Tablet, Intel i7, 16GB RAM, 256GB',
                'purchase_price' => 19500000,
                'selling_price' => 23500000,
                'min_stock' => 3,
                'current_stock' => 0, // OUT OF STOCK
                'unit' => 'pcs',
                'rack_location' => 'A1-04',
                'image_path' => $faker->imageUrl(640, 480, 'laptop', true, 'Surface Pro'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert semua produk sekaligus (lebih efisien)
        DB::table('products')->insert($products);
        
        // Tambahkan lebih banyak produk secara acak jika perlu
        $this->generateRandomProducts($categories, $faker, 30);

        // Generate summary statistics
        $totalProducts = Product::count();
        $lowStockCount = Product::whereRaw('current_stock <= min_stock AND current_stock > 0')->count();
        $outOfStockCount = Product::where('current_stock', 0)->count();
        $totalStockValue = Product::sum(DB::raw('current_stock * purchase_price'));

        $this->command->info('Products seeded successfully!');
        $this->command->info("Total: {$totalProducts} products created");
        $this->command->info("Low stock items: {$lowStockCount}");
        $this->command->info("Out of stock items: {$outOfStockCount}");
        $this->command->info("Total stock value: Rp " . number_format($totalStockValue, 0, ',', '.'));
        
        // Show some sample products
        $this->command->line('');
        $this->command->info('=== SAMPLE PRODUCTS ===');
        $sampleProducts = Product::with('category')->inRandomOrder()->limit(5)->get();
        foreach ($sampleProducts as $product) {
            $status = $product->current_stock == 0 ? '❌ Out of Stock' : 
                     ($product->current_stock <= $product->min_stock ? '⚠️ Low Stock' : '✅ Healthy');
            $this->command->info("• {$product->name} ({$product->sku})");
            $this->command->info("  Category: {$product->category->name}, Stock: {$product->current_stock} {$product->unit}");
            $this->command->info("  Status: {$status}");
            $this->command->info("  Image URL: {$product->image_path}");
            $this->command->info("");
        }
    }

    /**
     * Generate additional random products
     */
    private function generateRandomProducts($categories, $faker, $count = 30): void
    {
        $randomProducts = [];
        $brands = ['Dell', 'HP', 'Lenovo', 'ASUS', 'Acer', 'MSI', 'Logitech', 'Razer', 'Samsung', 'Apple', 'Sony', 'Bose'];
        
        for ($i = 0; $i < $count; $i++) {
            $category = $categories->random();
            $brand = $faker->randomElement($brands);
            $name = "{$brand} {$faker->words(2, true)}";
            
            $randomProducts[] = [
                'sku' => 'RND-' . strtoupper($faker->bothify('???-##??')),
                'name' => $name,
                'category_id' => $category->id,
                'description' => $faker->sentence(15),
                'purchase_price' => $faker->numberBetween(500000, 15000000),
                'selling_price' => $faker->numberBetween(750000, 20000000),
                'min_stock' => $faker->numberBetween(5, 20),
                'current_stock' => $faker->numberBetween(0, 50),
                'unit' => 'pcs',
                'rack_location' => $faker->bothify('?-##'),
                'image_path' => $faker->imageUrl(640, 480, 'electronics', true, $name),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        DB::table('products')->insert($randomProducts);
    }
}