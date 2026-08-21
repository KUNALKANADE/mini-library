<?php

namespace Tests\Feature\Api;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_books_index_returns_paginated_json(): void
    {
        Book::factory(3)->create([
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
        ]);

        $response = $this->getJson('/api/books');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'author', 'category', 'is_available'],
            ],
        ]);
        $response->assertJsonCount(3, 'data');
    }

    public function test_borrow_endpoint_requires_authentication(): void
    {
        $book = Book::factory()->create([
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
        ]);

        $response = $this->postJson("/api/books/{$book->id}/borrow");

        $response->assertUnauthorized(); // 401
    }

    public function test_authenticated_user_can_borrow_via_api(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'total_copies' => 1,
            'available_copies' => 1,
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/books/{$book->id}/borrow");

        $response->assertCreated(); // 201
        $this->assertDatabaseHas('loans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }
}
