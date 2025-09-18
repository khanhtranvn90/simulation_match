<?php

namespace App\Services;

use App\Models\MatchModel;
use App\Models\Team;
use App\Models\Standing;
use App\Repositories\MatchRepositoryInterface;
use App\DTO\WeekMatchDTO;

class MatchService implements MatchServiceInterface
{
    protected $matchRepo;
    protected $standingService;

    public function __construct(
        MatchRepositoryInterface $matchRepo,
        StandingServiceInterface $standingService
    ) {
        $this->matchRepo = $matchRepo;
        $this->standingService = $standingService;
    }

    /**
     * Simulate all matches in a given week.
     *
     * This method fetches all unsimulated matches for the specified week,
     * simulates each match, updates the match result, and updates the corresponding standings.
     *
     * @param int $week The week number to simulate
     */
    public function simulateWeek(int $week): void
    {
        $matches = $this->matchRepo->getMatchByWeek($week);

        foreach ($matches as $match) {
            $team1 = [
                Team::NAME => $match->team1->{Team::NAME},
                Team::STRENGTH => $match->team1->{Team::STRENGTH},
                Team::ID => $match->team1->{Team::ID}
            ];
            $team2 = [
                Team::NAME => $match->team2->{Team::NAME},
                Team::STRENGTH => $match->team2->{Team::STRENGTH},
                Team::ID => $match->team2->{Team::ID}
            ];

            $result = $this->simulateMatch($team1, $team2);

            // Update match record
            $match->{MatchModel::TEAM_1_SCORE} = $result[MatchModel::TEAM_1_SCORE];
            $match->{MatchModel::TEAM_2_SCORE} = $result[MatchModel::TEAM_2_SCORE];
            $match->{MatchModel::SIMULATED} = true;
            $match->save();

            // Update standings
            $this->standingService->updateStandings(
                $team1[Team::ID], 
                $team2[Team::ID], 
                $result[MatchModel::TEAM_1_SCORE], 
                $result[MatchModel::TEAM_2_SCORE]
            );
        }
    }

    /**
     * Simulate a single match between two teams (pure function).
     *
     * @param array $team1 ['name' => string, 'strength' => float]
     * @param array $team2 ['name' => string, 'strength' => float]
     * 
     * @return array Associative array with keys TEAM_1_SCORE and TEAM_2_SCORE
     */
    public function simulateMatch(array $team1, array $team2): array
    {
        $maxGoal = config('app.max_goal');
        $drawProb = config('app.draw_probability');
        // Check for draw first
        if (mt_rand() / mt_getrandmax() < $drawProb) {
            $score = mt_rand(0, 2);
            return [MatchModel::TEAM_1_SCORE => $score, MatchModel::TEAM_2_SCORE => $score];
        }

        $strength1 = $team1[Team::STRENGTH];
        $strength2 = $team2[Team::STRENGTH];

        $total = $strength1 + $strength2;
        $prob1 = $strength1 / $total;
        $prob2 = $strength2 / $total;

        $score1 = $this->randomGoal($prob1, $maxGoal);
        $score2 = $this->randomGoal($prob2, $maxGoal);

        // Avoid tie if strengths are different
        if ($score1 == $score2 && $strength1 != $strength2) {
            if ($strength1 > $strength2) $score1++;
            else $score2++;
        }

        return [MatchModel::TEAM_1_SCORE => $score1, MatchModel::TEAM_2_SCORE => $score2];
    }

    /**
     * Randomly generate the number of goals for a team based on its probability.
     *
     * @param float $prob Probability of scoring per attempt
     * @param int $maxGoal Maximum goal attempts
     * @return int Number of goals scored
     */
    protected function randomGoal(float $prob, int $maxGoal): int
    {
        $score = 0;
        for ($i = 0; $i < $maxGoal; $i++) {
            if (mt_rand() / mt_getrandmax() < $prob) $score++;
        }
        return $score;
    }

    /**
     * Get matches for the next week after the last simulated match.
     *
     * @return array<WeekMatchDTO>
     */
    public function currentWeekMatch(): array
    {
        $matches = $this->matchRepo->currentWeekMatch();
        $dtos = [];

        foreach ($matches as $match) {
            $dtos[] = WeekMatchDTO::fromArray([
                'id' => $match[MatchModel::ID] ?? null,
                'home' => $match['team1'][Team::NAME] ?? null,
                'home_score' => $match[MatchModel::TEAM_1_SCORE] ?? null,
                'away' => $match['team2'][Team::NAME] ?? null,
                'away_score' => $match[MatchModel::TEAM_2_SCORE] ?? null,
                'week' => $match[MatchModel::WEEK] ?? null,
            ]);
        }
        return $dtos;
    }

    public function nextWeekMatch()
    {
        $weekNum = $this->matchRepo->getNextWeekMatch();
        return $this->simulateWeek($weekNum);
    }
}
