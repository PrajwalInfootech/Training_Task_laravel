<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();

            $table->decimal('base_price', 10, 2);
            $table->decimal('discounted_price', 10, 2)->nullable();

            $table->unsignedInteger('stock')->default(0);

            $table->string('category');
            $table->string('status'); // published / draft

            $table->softDeletes(); // deleted_at
            $table->timestamps();

            // Indexes for dashboard & filters
            $table->unique('product_id'); // hasOne guarantee
            $table->index('category');
            $table->index('status');
            $table->index('stock');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_details');
    }
};
