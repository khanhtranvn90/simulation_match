<?php
namespace App\Services;

use App\DTO\WeekMatchDTO;

interface MatchServiceInterface
{
    public function simulateWeek(int $week);
    public function simulateMatch(array $team1, array $team2): array;
    public function currentWeekMatch(): array;
    public function nextWeekMatch();
}
