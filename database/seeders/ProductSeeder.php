<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductDetail;
class ProductSeeder extends Seeder
{
   public function run()
    {
        $category = ['men', 'women', 'kid'];
        $statuses = ['published', 'draft'];

        for ($i = 1; $i <= 150; $i++) {

            // 1️⃣ Create product
            $product = Product::create([
                'user_id'        => 1,
                'product_name'   => 'Product ' . $i,
                'sku'            => 'SKU-' . strtoupper(Str::random(6)),
                'barcode'        => null,
                'description'    => 'Description for Product ' . $i,
                'product_image'  => null,
                'created_at'     => now()->subDays(rand(0, 45)),
                'updated_at'     => now(),
            ]);

            // 2️⃣ Create product details
            ProductDetail::create([
                'product_id' => $product->id,
                'base_price'      => rand(499, 4999),
                'discounted_price'      => rand(499, 4999),
                'stock'      => rand(0, 2000),
                'status'     => $statuses[array_rand($statuses)],
                'category'     => $category[array_rand($category)],
                'created_at'=> $product->created_at,
                'updated_at'=> now(),
            ]);
        }
    }




}
