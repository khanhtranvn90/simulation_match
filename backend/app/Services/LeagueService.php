<?php

namespace App\Services;

use App\Models\Team;
use App\Models\MatchModel;
use App\Models\Standing;
use Illuminate\Support\Facades\DB;
use App\Repositories\StandingRepositoryInterface;
use App\Repositories\TeamRepositoryInterface;
use App\Repositories\MatchRepositoryInterface;
use App\DTO\ChampionDTO;
use InvalidArgumentException;

class LeagueService implements LeagueServiceInterface
{
    protected $standingRepo;
    protected $teamRepo;
    protected $matchRepo;

    public function __construct(
        StandingRepositoryInterface $standingRepo,
        TeamRepositoryInterface $teamRepo,
        MatchRepositoryInterface $matchRepo
    ) {
        $this->standingRepo = $standingRepo;
        $this->teamRepo = $teamRepo;
        $this->matchRepo = $matchRepo;
    }

    /**
     * Generate round-robin schedule for 4 teams
     */
    public function generateRoundRobin(int $leagueDuration): array
    {
        $teams = $this->teamRepo->all();
        if ($teams->count() % 2) {
            throw new \Exception("Teams count must be even");
        }

        $teamIds = $teams->pluck(Team::ID)->toArray();
        $rounds = $this->generateUniquePairs($teamIds);

        $allMatches = [];
        for ($week = 0; $week < $leagueDuration; $week++) {
            $roundIndex = $week % 3;
            foreach ($rounds[$roundIndex] as $pair) {
                $allMatches[] = \App\DTO\MatchDTO::fromArray([
                    MatchModel::TEAM_1 => $pair[0],
                    MatchModel::TEAM_2 => $pair[1],
                    MatchModel::WEEK => $week + 1,
                    MatchModel::TEAM_1_SCORE => null,
                    MatchModel::TEAM_2_SCORE => null,
                    MatchModel::SIMULATED => false
                ])->toArray();
            }
        }

        return $allMatches;
    }

    /**
     * Generate unique pairs of teams for matches
     * Ensures each team plays every other team exactly once in 3 rounds
     */
    private function generateUniquePairs(array $teamIds): array
    {
        if (count($teamIds) !== 4) {
            throw new InvalidArgumentException("Need exactly 4 teams to generate rounds.");
        }

        $rounds = [];
        $pairs = [];

        for ($i = 0; $i < 3; $i++) {
            for ($j = $i + 1; $j < 4; $j++) {
                $pairs[] = [$teamIds[$i], $teamIds[$j]];
            }
        }

        $usedPairs = [];
        while (count($usedPairs) < count($pairs)) {
            $round = [];
            foreach ($pairs as $key => $pair) {
                if (in_array($key, $usedPairs)) continue;

                $teamIdsInRound = array_merge(...$round);
                if (in_array($pair[0], $teamIdsInRound) || in_array($pair[1], $teamIdsInRound)) {
                    continue;
                }

                $round[] = $pair;
                $usedPairs[] = $key;

                if (count($round) === 2) break;
            }

            if (count($round) === 2) {
                $rounds[] = $round;
            }
        }

        return $rounds;
    }

    /**
     * Generate and save the full league schedule to the database
     */
    public function generateSchedule(int $leagueDuration): void
    {
        $matches = $this->generateRoundRobin($leagueDuration);

        DB::transaction(function () use ($matches) {
            foreach ($matches as $match) {
                $this->matchRepo->create($match);
            }
        });
    }

    /**
     * Determine the league champion based on current standings.
     * If there's a tie in points, use goal difference (GD) as tiebreaker.
     * 
     * @return ChampionDTO ChampionDTO as an associative array
     */
    public function getLeagueChampion(): ChampionDTO
    {
        $maxPoint = $this->standingRepo->max(Standing::POINT);

        $candidates = $this->standingRepo->getByCriteria(
            [Standing::POINT => $maxPoint],
            ['team']
        );

        if ($candidates->count() <= 1) {
            $champions = $candidates->first();
        } else {
            $maxGD = $candidates->max(Standing::GD);
            $champions = $candidates->where(Standing::GD, $maxGD)->first();
        }

        $championArr = [
            'name' => $champions->team->{Team::NAME} ?? '',
            'pts' => $champions->{Standing::POINT} ?? 0,
            'win' => $champions->{Standing::WIN} ?? 0,
            'loss' => $champions->{Standing::LOSE} ?? 0,
            'draw' => $champions->{Standing::DRAW} ?? 0,
            'played' => $champions->{Standing::PLAYED} ?? 0,
            'finished' => true
        ];
        return ChampionDTO::fromArray($championArr);
    }
}
