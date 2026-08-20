<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function makeBook(int $copies = 1): Book
    {
        return Book::factory()->create([
            'total_copies' => $copies,
            'available_copies' => $copies,
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
        ]);
    }

    public function test_member_can_borrow_an_available_book(): void
    {
        $member = User::factory()->create();
        $book = $this->makeBook(copies: 1);

        $response = $this->actingAs($member)->post(route('loans.borrow', $book));

        $response->assertRedirect(route('loans.index'));
        $this->assertDatabaseHas('loans', [
            'user_id' => $member->id,
            'book_id' => $book->id,
            'status' => 'active',
        ]);
        $this->assertEquals(0, $book->fresh()->available_copies);
    }

    public function test_member_cannot_borrow_an_unavailable_book(): void
    {
        $member = User::factory()->create();
        $book = $this->makeBook(copies: 0);

        $response = $this->actingAs($member)->post(route('loans.borrow', $book));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('loans', [
            'user_id' => $member->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_borrowing_sets_a_14_day_due_date(): void
    {
        $member = User::factory()->create();
        $book = $this->makeBook(copies: 1);

        $this->actingAs($member)->post(route('loans.borrow', $book));

        $loan = Loan::first();
        $this->assertEquals(
            now()->addDays(14)->toDateString(),
            $loan->due_at->toDateString()
        );
    }

    public function test_member_can_return_their_own_loan(): void
    {
        $member = User::factory()->create();
        $book = $this->makeBook(copies: 1);
        $book->decrement('available_copies'); // simulate it already being on loan

        $loan = Loan::factory()->create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($member)->post(route('loans.return', $loan));

        $response->assertRedirect(route('loans.index'));
        $this->assertEquals('returned', $loan->fresh()->status);
        $this->assertNotNull($loan->fresh()->returned_at);
        $this->assertEquals(1, $book->fresh()->available_copies);
    }

    public function test_member_cannot_return_another_users_loan(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = $this->makeBook(copies: 1);

        $loan = Loan::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($other)->post(route('loans.return', $loan));

        $response->assertForbidden();
        $this->assertEquals('active', $loan->fresh()->status);
    }

    public function test_cannot_return_an_already_returned_loan(): void
    {
        $member = User::factory()->create();
        $book = $this->makeBook(copies: 1);

        $loan = Loan::factory()->returned()->create([
            'user_id' => $member->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($member)->post(route('loans.return', $loan));

        $response->assertSessionHas('error');
    }
}
