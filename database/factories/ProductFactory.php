<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::query()->inRandomOrder()->first()?->id ?? Category::factory(),
            'name' => $name,
            'slug' => str()->slug($name),
            'description' => fake()->paragraph(),
            'short_description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 15, 500),
            'compare_price' => fake()->randomFloat(2, 20, 700),
            'stock' => fake()->numberBetween(0, 50),
            'image' => fake()->imageUrl(900, 900, 'products', true),
            'is_featured' => fake()->boolean(50),
            'is_active' => true,
            'sku' => fake()->unique()->bothify('SKU-####'),
        ];
    }
}
