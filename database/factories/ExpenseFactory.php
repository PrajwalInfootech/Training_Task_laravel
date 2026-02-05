<?php
namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id') ?? 1,
            'title' => $this->faker->words(2, true),
            'amount' => $this->faker->randomFloat(2, 500, 50000),
            'category' => $this->faker->randomElement([
                'marketing',
                'infrastructure',
                'salary',
                'logistics',
                'other'
            ]),
            'expense_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
