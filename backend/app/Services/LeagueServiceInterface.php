<?php
namespace App\Services;

use App\DTO\ChampionDTO;

interface LeagueServiceInterface
{
    public function generateRoundRobin(int $leagueDuration): array;
    public function generateSchedule(int $leagueDuration): void;
    public function getLeagueChampion(): ChampionDTO;
}
