<?php
namespace App\Services;

interface StandingServiceInterface {
    public function getStandings();
    public function updateStandings(int $team1Id, int $team2Id, int $score1, int $score2);
}
