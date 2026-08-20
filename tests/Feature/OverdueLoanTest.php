<?php

namespace Tests\Feature;

use App\Jobs\SendOverdueReminders;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OverdueLoanTest extends TestCase
{
    use RefreshDatabase;

    protected function makeLoan(string $dueAt, string $status = 'active'): Loan
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
        ]);

        return Loan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'due_at' => $dueAt,
            'status' => $status,
        ]);
    }

    public function test_command_marks_past_due_active_loans_as_overdue(): void
    {
        $overdueLoan = $this->makeLoan(now()->subDays(3)->toDateString());
        $notYetDueLoan = $this->makeLoan(now()->addDays(3)->toDateString());

        $this->artisan('loans:check-overdue')->assertSuccessful();

        $this->assertEquals('overdue', $overdueLoan->fresh()->status);
        $this->assertEquals('active', $notYetDueLoan->fresh()->status);
    }

    public function test_command_does_not_touch_already_returned_loans(): void
    {
        $returnedLoan = $this->makeLoan(now()->subDays(5)->toDateString(), status: 'returned');

        $this->artisan('loans:check-overdue')->assertSuccessful();

        $this->assertEquals('returned', $returnedLoan->fresh()->status);
    }

    public function test_command_dispatches_the_reminder_job_when_overdue_loans_exist(): void
    {
        Queue::fake();

        $this->makeLoan(now()->subDays(1)->toDateString());

        $this->artisan('loans:check-overdue');

        Queue::assertPushed(SendOverdueReminders::class);
    }

    public function test_command_does_not_dispatch_the_job_when_nothing_is_overdue(): void
    {
        Queue::fake();

        $this->makeLoan(now()->addDays(5)->toDateString());

        $this->artisan('loans:check-overdue');

        Queue::assertNotPushed(SendOverdueReminders::class);
    }
}
