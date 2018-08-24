<?php

namespace App\Console;

use App\Console\Commands\FaAccountInsight;
use App\Console\Commands\FAInsightReader;
use App\Console\Commands\FbAdReader;
use App\Console\Commands\GetInsightAd;
use App\Console\Commands\LeadUpdateFee;
use App\Console\Commands\RbacAttachPermissionGroupRole;
use App\Console\Commands\RbacAttachUserRole;
use App\Console\Commands\RbacPermissionGenerate;
use App\Console\Commands\RbacPermissionGroupGenerate;
use App\Console\Commands\RbacRoleGenerate;
use App\Console\Commands\UpdateData;
use App\Console\Commands\UpdateLeadDuplicate;
use App\Console\Commands\UpdateLeadToTimeout;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

        RbacRoleGenerate::class,
        RbacPermissionGenerate::class,
        RbacPermissionGroupGenerate::class,
        RbacAttachPermissionGroupRole::class,
        RbacAttachUserRole::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

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
