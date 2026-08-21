<?php

namespace App\Http\Controllers\Api;

use App\Events\BookBorrowed;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Http\Resources\LoanResource;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with(['author', 'category'])
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$request->input('search').'%')
            )
            ->paginate(15);

        return BookResource::collection($books);
    }

    public function show(Book $book)
    {
        $book->load(['author', 'category']);

        return new BookResource($book);
    }

    public function borrow(Request $request, Book $book)
    {
        $this->authorize('create', Loan::class);

        if (! $book->isAvailable()) {
            return response()->json([
                'message' => 'This book is not currently available to borrow.',
            ], 422);
        }

        $existingLoan = $book->loans()
            ->where('user_id', $request->user()->id)
            ->where('status', '!=', 'returned')
            ->exists();

        if ($existingLoan) {
            return response()->json([
                'message' => 'You already have an active loan for this book.',
            ], 422);
        }

        $loan = DB::transaction(function () use ($book, $request) {
            $book->decrement('available_copies');

            return Loan::create([
                'user_id' => $request->user()->id,
                'book_id' => $book->id,
                'borrowed_at' => now(),
                'due_at' => now()->addDays(14),
                'status' => 'active',
            ]);
        });

        event(new BookBorrowed($loan));

        return response()->json([
            'message' => 'Book borrowed successfully.',
            'data' => new LoanResource($loan),
        ], 201);
    }
}
