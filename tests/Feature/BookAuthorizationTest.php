<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_librarian_can_create_book(): void
    {
        $librarian = User::factory()->librarian()->create();
        $author = Author::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($librarian)->post(route('books.store'), [
            'title' => 'Test Book',
            'total_copies' => 3,
            'author_id' => $author->id,
            'category_id' => $category->id,
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', ['title' => 'Test Book']);
    }

    public function test_member_cannot_create_book(): void
    {
        $member = User::factory()->create(); // default role: member
        $author = Author::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($member)->post(route('books.store'), [
            'title' => 'Should Fail',
            'total_copies' => 3,
            'author_id' => $author->id,
            'category_id' => $category->id,
        ]);

        $response->assertForbidden(); // 403
        $this->assertDatabaseMissing('books', ['title' => 'Should Fail']);
    }

    public function test_member_cannot_view_another_users_loan(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create();

        $loan = \App\Models\Loan::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
        ]);

        // once you have a loans.show route wired up in Day 8:
        $response = $this->actingAs($other)->get(route('loans.show', $loan));

        $response->assertForbidden();
    }
}
