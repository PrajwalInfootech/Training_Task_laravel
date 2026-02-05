<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
   public function definition()
{
    return [
        'user_id' => 1, // since you said only one user for now
        'product_name' => $this->faker->words(3, true),
        'sku' => strtoupper(Str::random(8)),
        'barcode' => $this->faker->ean13(),
        'description' => $this->faker->paragraph(),
        'product_image' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ];
}
}
