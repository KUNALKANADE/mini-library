<?php

namespace App\Http\Controllers\Api;

use App\Events\BookReturned;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $loans = $request->user()->loans()
            ->with('book')
            ->latest('borrowed_at')
            ->paginate(15);

        return LoanResource::collection($loans);
    }

    public function return(Request $request, Loan $loan)
    {
        $this->authorize('update', $loan);

        if ($loan->status === 'returned') {
            return response()->json([
                'message' => 'This loan has already been returned.',
            ], 422);
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

        return response()->json([
            'message' => 'Book returned successfully.',
            'data' => new LoanResource($loan->fresh()),
        ]);
    }
}
