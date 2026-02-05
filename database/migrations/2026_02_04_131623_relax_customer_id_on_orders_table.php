<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('orders', function (Blueprint $table) {
        // Drop FK
        $table->dropForeign(['customer_id']);

        // Keep column, but allow nullable
        $table->unsignedBigInteger('customer_id')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->unsignedBigInteger('customer_id')->nullable(false)->change();
        $table->foreign('customer_id')->references('id')->on('users')->cascadeOnDelete();
    });
}

};
