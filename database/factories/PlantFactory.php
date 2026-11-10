<?php

namespace Database\Factories;

use App\Models\Plant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plant>
 */
class PlantFactory extends Factory
{
    protected $model = Plant::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->word(),
            'category' => fake()->randomElement(['Ornamental', 'Flowering', 'Succulent', 'Herb']),
            'unit_price' => fake()->randomFloat(2, 0, 500),
            'description' => fake()->sentence(),
            'stock_status' => fake()->randomElement(['in_stock', 'out_of_stock']),
        ];
    }
}
