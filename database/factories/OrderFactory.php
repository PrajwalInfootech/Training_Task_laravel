<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Product;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $userId = User::query()->select('id')->inRandomOrder()->first()?->id;
        $productId = Product::query()->select('id')->inRandomOrder()->first()?->id;

        if (!$userId || !$productId) {
            throw new \Exception('Users or Products table is empty. Seed them first.');
        }

        return [
            'user_id'     => $userId,
            'product_id'  => $productId,
        'customer_id' => User::inRandomOrder()->value('id'), // nullable-safe
            'quantity'    => $this->faker->numberBetween(1, 5),
            'total_amount'=> $this->faker->numberBetween(100, 5000),
            'status'      => $this->faker->randomElement(['pending', 'completed']),
        ];
    }
}
