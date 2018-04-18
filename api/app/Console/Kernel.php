<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\GenerateRateLog::class,
        Commands\GenerateBolReport::class,
        Commands\UpdateLocalAvatar::class,
        Commands\TestFunction::class,
        Commands\ExportBillDailyAll::class,
        Commands\ExportBillDailySingle::class,
        Commands\GenerateBolDaily::class,
        Commands\CheckCompleteOrder::class,
        Commands\UpdateExportBillId::class,
        Commands\UpdateOrderItemNote::class,
        Commands\GetDuplicateBol::class,
        Commands\UpdateExportBill::class,
        Commands\CorrectMatchCnBol::class,
        Commands\UpdateOrderStatistics::class,
        Commands\ResetDb::class,
        Commands\RefreshExport::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
