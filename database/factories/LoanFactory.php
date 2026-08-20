<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    public function definition(): array
    {
        $borrowedAt = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'borrowed_at' => $borrowedAt,
            'due_at' => (clone $borrowedAt)->modify('+14 days'),
            'returned_at' => null,
            'status' => 'active',
        ];
    }

    public function returned(): static
    {
        return $this->state(fn () => [
            'returned_at' => fake()->dateTimeBetween($this->faker->dateTimeThisMonth(), 'now'),
            'status' => 'returned',
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'due_at' => now()->subDays(3),
            'status' => 'overdue',
        ]);
    }
}
