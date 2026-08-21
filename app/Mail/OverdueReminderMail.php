<?php

namespace App\Mail;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OverdueReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Loan $loan) {}

    public function build()
    {
        return $this->subject('Overdue: '.$this->loan->book->title)
            ->view('emails.overdue-reminder');
    }
}
