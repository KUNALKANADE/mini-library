<?php

namespace App\Mail;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookBorrowedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Loan $loan) {}

    public function build()
    {
        return $this->subject('You borrowed: '.$this->loan->book->title)
            ->view('emails.book-borrowed');
    }
}
