<?php

namespace App\Http\Controllers;

use App\Events\BookBorrowed;
use App\Events\BookReturned;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index()
    {
        $loans = auth()->user()->loans()
            ->with('book')
            ->latest('borrowed_at')
            ->paginate(15);

        return view('loans.index', compact('loans'));
    }

    public function show(Loan $loan)
    {
        $this->authorize('view', $loan);

        $loan->load('book');

        return view('loans.show', compact('loan'));
    }

    public function borrow(Book $book)
    {
        $this->authorize('create', Loan::class);

        if (! $book->isAvailable()) {
            return back()->with('error', 'This book is not currently available to borrow.');
        }

        $existingLoan = $book->loans()
            ->where('user_id', auth()->id())
            ->where('status', '!=', 'returned')
            ->exists();

        if ($existingLoan) {
            return back()->with('error', 'You already have an active loan for this book.');
        }

        $loan = DB::transaction(function () use ($book) {
            $book->decrement('available_copies');

            return Loan::create([
                'user_id' => auth()->id(),
                'book_id' => $book->id,
                'borrowed_at' => now(),
                'due_at' => now()->addDays(14),
                'status' => 'active',
            ]);
        });

        event(new BookBorrowed($loan));

        return redirect()->route('loans.index')
            ->with('success', "You've borrowed \"{$book->title}\". Due back in 14 days.");
    }

    public function return(Loan $loan)
    {
        $this->authorize('update', $loan);

        if ($loan->status === 'returned') {
            return back()->with('error', 'This loan has already been returned.');
        }

        DB::transaction(function () use ($loan) {
            $loan->update([
                'returned_at' => now(),
                'status' => 'returned',
            ]);

            $loan->book->increment('available_copies');

            $nextReservation = $loan->book->reservations()
                ->where('status', 'active')
                ->oldest()
                ->first();

            if ($nextReservation) {
                $nextReservation->update(['status' => 'fulfilled']);
            }
        });

        event(new BookReturned($loan));

        return redirect()->route('loans.index')
            ->with('success', "You've returned \"{$loan->book->title}\".");
    }
}
