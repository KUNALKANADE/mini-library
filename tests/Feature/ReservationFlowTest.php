<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function makeUnavailableBook(): Book
    {
        return Book::factory()->create([
            'total_copies' => 1,
            'available_copies' => 0,
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
        ]);
    }

    public function test_member_can_reserve_an_unavailable_book(): void
    {
        $member = User::factory()->create();
        $book = $this->makeUnavailableBook();

        $response = $this->actingAs($member)->post(route('reservations.reserve', $book));

        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseHas('reservations', [
            'user_id' => $member->id,
            'book_id' => $book->id,
            'status' => 'active',
        ]);
    }

    public function test_member_cannot_reserve_an_available_book(): void
    {
        $member = User::factory()->create();
        $book = Book::factory()->create([
            'total_copies' => 1,
            'available_copies' => 1,
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
        ]);

        $response = $this->actingAs($member)->post(route('reservations.reserve', $book));

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $member->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_duplicate_active_reservations_are_prevented(): void
    {
        $member = User::factory()->create();
        $book = $this->makeUnavailableBook();

        Reservation::factory()->create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($member)->post(route('reservations.reserve', $book));

        $response->assertSessionHas('error');
        $this->assertEquals(
            1,
            Reservation::where('user_id', $member->id)->where('book_id', $book->id)->count()
        );
    }

    public function test_oldest_active_reservation_is_fulfilled_on_return(): void
    {
        $borrower = User::factory()->create();
        $firstInLine = User::factory()->create();
        $secondInLine = User::factory()->create();

        $book = Book::factory()->create([
            'total_copies' => 1,
            'available_copies' => 0,
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
        ]);

        $loan = Loan::factory()->create([
            'user_id' => $borrower->id,
            'book_id' => $book->id,
            'status' => 'active',
        ]);

        $firstReservation = Reservation::factory()->create([
            'user_id' => $firstInLine->id,
            'book_id' => $book->id,
            'status' => 'active',
            'created_at' => now()->subDays(2),
        ]);

        $secondReservation = Reservation::factory()->create([
            'user_id' => $secondInLine->id,
            'book_id' => $book->id,
            'status' => 'active',
            'created_at' => now()->subDay(),
        ]);

        $this->actingAs($borrower)->post(route('loans.return', $loan));

        $this->assertEquals('fulfilled', $firstReservation->fresh()->status);
        $this->assertEquals('active', $secondReservation->fresh()->status);
    }
}
