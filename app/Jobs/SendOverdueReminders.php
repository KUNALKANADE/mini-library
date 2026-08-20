<?php

namespace App\Jobs;

use App\Mail\OverdueReminderMail;
use App\Models\Loan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOverdueReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $tries = 3;

    public function handle(): void
    {
        $overdueLoans = Loan::where('status', 'overdue')
            ->with(['user', 'book'])
            ->get();

        foreach ($overdueLoans as $loan) {
            Mail::to($loan->user)->send(new OverdueReminderMail($loan));
        }
    }
}
