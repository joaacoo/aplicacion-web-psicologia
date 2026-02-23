<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
Schedule::command('app:process-appointments')->everyMinute();
Schedule::command('app:cleanup-old-proofs')->daily();
Schedule::command('appointments:mark-completed')->everyThirtyMinutes();
Schedule::command('appointments:cancel-unpaid')->everyFifteenMinutes();


