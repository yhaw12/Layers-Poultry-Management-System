<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        $sourceTypes = [
            Sale::class => ['type' => 'sale', 'source' => Sale::inRandomOrder()->first()],
            Expense::class => ['type' => 'expense', 'source' => Expense::inRandomOrder()->first()],
            Income::class => ['type' => 'income', 'source' => Income::inRandomOrder()->first()],
            Order::class => ['type' => 'order', 'source' => Order::inRandomOrder()->first()],
        ];
        $selected = $this->faker->randomElement(array_keys($sourceTypes));
        $source = $sourceTypes[$selected]['source'];

        return [
            'type' => $sourceTypes[$selected]['type'],
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'source_type' => $source ? $selected : null,
            'source_id' => $source ? $source->id : null,
            'user_id' => User::inRandomOrder()->first()?->id,
            'description' => $this->faker->sentence(),
        ];
    }
}