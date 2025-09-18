<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LeagueServiceInterface;

class GenerateScheduleCommand extends Command
{
    protected $signature = 'league:generate-schedule';
    protected $description = 'Generate league schedule for given number of weeks';

    public function handle(LeagueServiceInterface $leagueService)
    {
        $weeks = (int) config('app.league_duration');
        $leagueService->generateSchedule($weeks);

        $this->info("League schedule generated for {$weeks} weeks.");
    }
}
