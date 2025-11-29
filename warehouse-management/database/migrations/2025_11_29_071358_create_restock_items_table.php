<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('restock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restock_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            // $table->integer('unit_price')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('restock_items');
    }
};
