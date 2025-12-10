<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();

            $table->enum('type', ['incoming', 'outgoing']);
            $table->date('date');

            $table->foreignId('supplier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name')->nullable();

            $table->text('notes')->nullable();
            $table->enum('status', ['Pending', 'Verified', 'Completed', 'Approved', 'Shipped'])->default('Pending');

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
