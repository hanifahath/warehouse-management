<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['incoming', 'outgoing', 'adjustment']);
            $table->integer('quantity');
            $table->string('reference_type')->nullable(); // misal 'Transaction', 'RestockOrder'
            $table->unsignedBigInteger('reference_id')->nullable(); // id dari transaksi/PO
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // siapa yang melakukan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
