<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('product_name');
            $table->string('sku')->unique();

            $table->string('barcode')->nullable();

            $table->text('description')->nullable();

            $table->string('product_image')->nullable();

            $table->softDeletes(); // deleted_at
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('product_name');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
