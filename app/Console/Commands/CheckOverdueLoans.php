<?php

namespace App\Console\Commands;

use App\Jobs\SendOverdueReminders;
use App\Models\Loan;
use Illuminate\Console\Command;

class CheckOverdueLoans extends Command
{
    protected $signature = 'loans:check-overdue';

    protected $description = 'Mark active loans as overdue when past their due date, and queue reminder emails.';

    public function handle(): int
    {
        $overdueLoans = Loan::where('status', 'active')
            ->where('due_at', '<', now())
            ->get();

        if ($overdueLoans->isEmpty()) {
            $this->info('No overdue loans found.');
            return self::SUCCESS;
        }

        foreach ($overdueLoans as $loan) {
            $loan->update(['status' => 'overdue']);
        }

        $this->info("{$overdueLoans->count()} loan(s) marked as overdue.");

        SendOverdueReminders::dispatch();

        $this->info('Overdue reminder job dispatched.');

        return self::SUCCESS;
    }
}
