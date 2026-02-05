<?php

namespace Database\Seeders;
   use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

public function run(): void
{
    $customerIds = DB::table('orders')
        ->whereNotNull('customer_id')
        ->pluck('customer_id')
        ->unique();

    foreach ($customerIds as $id) {
        DB::table('customers')->updateOrInsert(
            ['id' => $id],
            [
                'name' => 'Customer '.$id,
                'email' => "customer{$id}@example.com",
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}

}
