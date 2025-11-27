<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // 1. IDENTITAS & KUNCI (WAJIB)
            $table->string('sku')->unique(); // Stock Keeping Unit - unique identifier
            $table->string('name');
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete(); // Kategori (dropdown)
            
            // 2. STOK & LOKASI
            $table->integer('stock')->default(0);       // Stok saat ini (current_stock)
            $table->integer('min_stock')->default(0);   // Stok minimum (untuk alert)
            $table->string('unit')->default('pcs');     // Unit (pcs, box, kg, liter, dll)
            $table->string('location')->nullable();     // Lokasi rak di gudang

            // 3. DETAIL & HARGA
            $table->decimal('purchase_price', 15, 2)->default(0); // Harga beli
            $table->decimal('selling_price', 15, 2)->default(0);  // Harga jual
            $table->text('description')->nullable();    // Deskripsi (Gunakan text untuk deskripsi panjang)
            $table->string('image_path')->nullable();   // Gambar produk

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};