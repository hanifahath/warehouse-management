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
            $table->string('sku')->unique();                  // unique identifier
            $table->string('name');                           // nama produk
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 12, 2);
            $table->decimal('selling_price', 12, 2);
            $table->integer('min_stock')->default(0);         // stok minimum
            $table->integer('stock')->default(0);             // stok saat ini
            $table->string('unit');                           // pcs, box, kg, dll
            $table->string('location')->nullable();           // lokasi rak
            $table->string('image_path')->nullable();         // gambar produk
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};