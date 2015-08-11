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
        \App\Console\Commands\Inspire::class,
        //店铺往来数据相关
        \App\Console\Commands\ShopcountCreate::class,
        \App\Console\Commands\ShopcountIndex::class,
        \App\Console\Commands\ShopcountPreview::class,
        \App\Console\Commands\ShopcountShow::class,
        \App\Console\Commands\ShopcountStore::class,
        \App\Console\Commands\ShopcountUpdate::class,
        \App\Console\Commands\ShopcountDestory::class,
        \App\Console\Commands\ShopcountBalance::class,
        \App\Console\Commands\ShopcountDelegateDetail::class,
        \App\Console\Commands\ShopcountDelegateList::class,
        \App\Console\Commands\ShopcountCountBalance::class,
        \App\Console\Commands\ShopcountCountBountyBalance::class,
        \App\Console\Commands\ShopcountImportPrepay::class,
        \App\Console\Commands\ShopcountFastCountOrder::class,
        \App\Console\Commands\ShopcountDelegateExport::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();
    }
}
