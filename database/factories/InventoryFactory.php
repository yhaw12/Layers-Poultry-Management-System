<?php

namespace Database\Factories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'sku' => 'SKU-' . $this->faker->unique()->numberBetween(1000, 9999),
            'qty' => $this->faker->numberBetween(10, 1000),
            'threshold'=> $this->faker->numberBetween(10, 50),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}