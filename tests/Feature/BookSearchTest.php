<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_filters_books_by_title(): void
    {
        $user = User::factory()->create();
        $author = Author::factory()->create();
        $category = Category::factory()->create();

        Book::factory()->create([
            'title' => 'Laravel Fundamentals',
            'author_id' => $author->id,
            'category_id' => $category->id,
        ]);
        Book::factory()->create([
            'title' => 'PHP Basics',
            'author_id' => $author->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)->get(route('books.index', ['search' => 'Laravel']));

        $response->assertSee('Laravel Fundamentals');
        $response->assertDontSee('PHP Basics');
    }
}
