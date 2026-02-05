<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // seller (logged-in user)
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // buyer
            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();

            // product sold
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // sales data
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('total_amount', 10, 2);

            // analytics-safe status
            $table->enum('status', ['completed', 'cancelled'])
                ->default('completed');

            $table->timestamps();

            // performance indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
