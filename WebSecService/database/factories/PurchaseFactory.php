<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory()->create();
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'price_at_purchase' => $this->faker->randomFloat(2, 10, 1000),
            'purchase_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
