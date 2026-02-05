<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['customer_id']);

            // Re-add foreign key pointing to users table
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Rollback: remove FK to users
            $table->dropForeign(['customer_id']);

            // Restore original FK (customers table)
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->cascadeOnDelete();
        });
    }
};
