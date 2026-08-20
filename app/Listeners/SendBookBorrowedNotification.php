<?php

namespace App\Listeners;

use App\Events\BookBorrowed;
use App\Mail\BookBorrowedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendBookBorrowedNotification implements ShouldQueue
{
    public function handle(BookBorrowed $event): void
    {
        Mail::to($event->loan->user)->send(new BookBorrowedMail($event->loan));
    }
}
