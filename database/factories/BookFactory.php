<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition(): array
    {
        $total = fake()->numberBetween(1, 5);

        return [
            'title' => fake()->sentence(4),
            'isbn' => fake()->unique()->isbn13(),
            'description' => fake()->paragraph(),
            'total_copies' => $total,
            'available_copies' => $total,
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
        ];
    }
}
