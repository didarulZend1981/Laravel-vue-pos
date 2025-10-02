<?php



namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel{
    protected $commands = [
        \App\Console\Commands\MoveExpiredProductsToWaste::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('products:move-expired-to-waste')->everyMinute();

        $schedule->call(function () {
            \Log::info('Schedule is running at ' . now());
        })->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
