<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductDetailFactory extends Factory
{
  public function definition()
{
    return [
        'price' => $this->faker->numberBetween(199, 9999),
        'stock' => $this->faker->numberBetween(0, 2000),
        'status' => $this->faker->randomElement(['published', 'draft']),
        'category' => $this->faker->randomElement(['men', 'women', 'kids']),
        'created_at' => now(),
        'updated_at' => now(),
    ];
}
}
