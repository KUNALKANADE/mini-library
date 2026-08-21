<?php

namespace App\Http\Controllers;

use App\Events\BookReserved;
use App\Models\Book;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = auth()->user()->reservations()
            ->with('book')
            ->latest()
            ->paginate(15);

        return view('reservations.index', compact('reservations'));
    }

    public function reserve(Book $book)
    {
        $this->authorize('create', Reservation::class);

        if ($book->isAvailable()) {
            return back()->with('error', 'This book is currently available — no need to reserve, you can borrow it directly.');
        }

        $alreadyReserved = $book->reservations()
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->exists();

        if ($alreadyReserved) {
            return back()->with('error', 'You already have an active reservation for this book.');
        }

        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'status' => 'active',
        ]);

        event(new BookReserved($reservation));

        return redirect()->route('reservations.index')
            ->with('success', "You've reserved \"{$book->title}\". You'll be notified when a copy is available.");
    }

    public function cancel(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        $reservation->update(['status' => 'cancelled']);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation cancelled.');
    }
}
