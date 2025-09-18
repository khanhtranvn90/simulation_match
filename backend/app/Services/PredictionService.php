<?php

namespace App\Services;

use App\Models\Standing;
use App\Models\Team;
use App\Models\MatchModel;
use App\Repositories\MatchRepositoryInterface;
use App\Services\PredictionCacheServiceInterface;
use App\Services\MatchServiceInterface;
use App\DTO\PredictionsDTO;

class PredictionService implements PredictionServiceInterface
{
    protected MatchServiceInterface $matchService;
    protected MatchRepositoryInterface $matchRepository;
    protected PredictionCacheServiceInterface $cache;

    public function __construct(
        MatchServiceInterface $matchService,
        MatchRepositoryInterface $matchRepository,
        PredictionCacheServiceInterface $cache
    )
    {
        $this->matchService = $matchService;
        $this->matchRepository = $matchRepository;
        $this->cache = $cache;
    }


    /**
     * Get prediction for the league based on current week
     */
    public function getPredictionForLeague(): array
    {
        $currentWeek = $this->matchRepository->currentWeek();
        $leagueDuration = config('app.league_duration');

        if ($currentWeek < 4) {
            echo "Not enough matches to simulate.\n";
            return [];
        }

        if ($currentWeek > $leagueDuration) {
            echo "League already finished.\n";
            return [];
        }

        if ($this->cache->hasWeek($currentWeek)) {
            return $this->cache->getWeekData($currentWeek);
        }

        $data = $this->simulateLeague($currentWeek);

        $this->cache->setWeekData($currentWeek, $data);

        return $data;
    }

    /**
     * Simulate the rest of the league using Monte Carlo
     *
     * @param int $currentWeek Total number of weeks
     * 
     * @return array List of PredictionsDTO with team name and winning probability
     */
    private function simulateLeague(int $currentWeek): array
    {

        $leagueDuration = config('app.league_duration');
        $mcSimulations = config('app.mc_simulations');

        // Load current standings (points so far) and team strength
        $standings = Standing::with('team')->get();
        $teams = $standings->pluck('team')->keyBy(Standing::TEAM_ID);
        $teamStats = [];
        $allMatches = $this->matchRepository->getAllMatches();

        foreach ($standings as $s) {
            $teamStats[$s->{Standing::TEAM_ID}] = [
                Standing::POINT => $s->{Standing::POINT},
                Standing::PLAYED => $s->{Standing::PLAYED},
                Team::STRENGTH => $s->team->{Team::STRENGTH},
                Team::NAME => $s->team->{Team::NAME}
            ];
        }

        // Initialize wins counter for Monte Carlo
        $wins = array_fill_keys(array_keys($teamStats), 0);

        // Run Monte Carlo simulations
        for ($i = 0; $i < $mcSimulations; $i++) {

            // Copy points for simulation
            $simPoints = [];
            foreach ($teamStats as $tid => $stat) {
                $simPoints[$tid] = $stat[Standing::POINT];
            }

            // Simulate each week
            for ($w = $currentWeek; $w <= $leagueDuration; $w++) {
                $matches = array_filter($allMatches, fn($m) => $m['week'] === $w);

                foreach ($matches as $match) {

                    // dd($match, $teamStats, $allMatches);

                    $score = $this->matchService->simulateMatch(
                        [
                            'id' => $match['team_1'],
                            'strength' => $teamStats[$match['team_1']][Team::STRENGTH],
                            'name' => $teamStats[$match['team_1']][Team::NAME]
                        ],
                        [
                            'id' => $match['team_2'],
                            'strength' => $teamStats[$match['team_2']][Team::STRENGTH],
                            'name' => $teamStats[$match['team_2']][Team::NAME]
                        ]
                    );

                    if ($score[MatchModel::TEAM_1_SCORE] > $score[MatchModel::TEAM_2_SCORE]) {
                        $simPoints[$match['team_1']] += 3;
                    } elseif ($score[MatchModel::TEAM_1_SCORE] < $score[MatchModel::TEAM_2_SCORE]) {
                        $simPoints[$match['team_2']] += 3;
                    } else {
                        $simPoints[$match['team_1']] += 1;
                        $simPoints[$match['team_2']] += 1;
                    }
                }
            }

            // Determine winner(s)
            $maxPoints = max($simPoints);
            $winners = array_keys(array_filter($simPoints, fn($p) => $p === $maxPoints));

            // Randomly pick one if tie
            $wins[$winners[array_rand($winners)]]++;
        }

            // dd($matches);

        // Print winning probabilities
        $results = [];
        foreach ($wins as $tid => $count) {
            $prob = round($count / $mcSimulations * 100);
            $results[] = PredictionsDTO::fromArray([
                'name' => $teamStats[$tid][Team::NAME],
                'percentage' => $prob
            ]);
        }
            // dd($wins, array_sum($wins), $results);

        return $results;
    }
}
