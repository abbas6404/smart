<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;

class Schedule
{
    public function register(Schedule $schedule): void
    {
        // Run AutoBoard distribution every day at midnight (00:00)
        $schedule->command('auto-board:process')
            ->daily()
            ->at('00:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->onSuccess(function () {
                \Log::info('AutoBoard: Scheduled distribution completed successfully');
            })
            ->onFailure(function () {
                \Log::error('AutoBoard: Scheduled distribution failed');
            });
    }
}
