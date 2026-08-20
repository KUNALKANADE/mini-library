<?php
use App\Console\Commands\CheckOverdueLoans;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CheckOverdueLoans::class)->daily();
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
