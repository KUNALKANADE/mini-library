<?php

namespace App\Listeners;

use App\Events\BookReturned;
use App\Mail\BookAvailableMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendBookAvailableNotification implements ShouldQueue
{
    public function handle(BookReturned $event): void
    {
        $nextReservation = $event->loan->book->reservations()
            ->where('status', 'fulfilled')
            ->latest('updated_at')
            ->first();

        if ($nextReservation) {
            Mail::to($nextReservation->user)->send(new BookAvailableMail($nextReservation));
        }
    }
}
