<?php

namespace Tests\Feature;

use App\Mail\BookAvailableMail;
use App\Mail\BookBorrowedMail;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrowing_a_book_sends_confirmation_email(): void
{
    $this->withoutExceptionHandling();

    Mail::fake();

    $member = User::factory()->create();
    $book = Book::factory()->create([
        'total_copies' => 1,
        'available_copies' => 1,
        'author_id' => Author::factory(),
        'category_id' => Category::factory(),
    ]);

    $response = $this->actingAs($member)->post(route('loans.borrow', $book));

    Mail::assertSent(BookBorrowedMail::class, function ($mail) use ($member) {
        return $mail->hasTo($member->email);
    });
}

    public function test_returning_a_book_notifies_the_next_reserved_member(): void
    {
        Mail::fake();

        $borrower = User::factory()->create();
        $waitingMember = User::factory()->create();

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

        Reservation::factory()->create([
            'user_id' => $waitingMember->id,
            'book_id' => $book->id,
            'status' => 'active',
        ]);

        $this->actingAs($borrower)->post(route('loans.return', $loan));

        Mail::assertSent(BookAvailableMail::class, function ($mail) use ($waitingMember) {
            return $mail->hasTo($waitingMember->email);
        });
    }
}
