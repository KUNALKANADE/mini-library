<?php

namespace Tests\Feature;

use App\Events\BookBorrowed;
use App\Listeners\SendBookBorrowedNotification;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class QueuedListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrowing_a_book_dispatches_the_book_borrowed_event(): void
    {
        Event::fake();

        $member = User::factory()->create();
        $book = Book::factory()->create([
            'total_copies' => 1,
            'available_copies' => 1,
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
        ]);

        $this->actingAs($member)->post(route('loans.borrow', $book));

        Event::assertDispatched(BookBorrowed::class);
    }
}
