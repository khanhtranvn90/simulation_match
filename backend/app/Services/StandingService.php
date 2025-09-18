<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Repositories\StandingRepositoryInterface;
use App\Repositories\TeamRepositoryInterface;
use App\Models\Standing;
use App\Models\Team;
use App\DTO\StandingDTO;

class StandingService implements StandingServiceInterface {
    protected $repo;
    protected $teamRepo;

    public function __construct(
        StandingRepositoryInterface $repo,
        TeamRepositoryInterface $teamRepo
    )
    {
        $this->repo = $repo;
        $this->teamRepo = $teamRepo;
    }

    /**
     * Get the current standings.
     * 
     * @return StandingDTO[] Array of StandingDTO objects representing the standings
     */
    public function getStandings(): array
    {
        $teams = $this->teamRepo->all()->keyBy(Team::ID);
        $standings = $this->repo->all();
        $result = [];
        foreach ($standings as $standing) {
            $teamId = $standing[Standing::TEAM_ID];
            $team = isset($teams[$teamId]) ? $teams[$teamId] : null;
            $result[] = StandingDTO::fromArray([
                'team_id' => $teamId,
                'team_name' => $team ? $team[Team::NAME] : null,
                'played' => $standing[Standing::PLAYED],
                'wins' => $standing[Standing::WIN],
                'draws' => $standing[Standing::DRAW],
                'losses' => $standing[Standing::LOSE],
                'goals_for' => $standing[Standing::GOAL_FOR],
                'goals_against' => $standing[Standing::GOAL_AGAINST],
                'goal_difference' => $standing[Standing::GD],
                'points' => $standing[Standing::POINT],
            ]);
        }
        return $result;
    }

    /**
     * Update the standings for two teams after a match.
     *
     * This method updates played matches, wins, draws, losses, points,
     * goals for/against, and goal difference.
     *
     * @param int $team1Id
     * @param int $team2Id
     * @param int $score1
     * @param int $score2
     */
    public function updateStandings(int $team1Id, int $team2Id, int $score1, int $score2): void
    {
        $standing1 = Standing::firstOrCreate([Standing::TEAM_ID => $team1Id]);
        $standing2 = Standing::firstOrCreate([Standing::TEAM_ID => $team2Id]);

        // Update matches played
        $standing1->played++;
        $standing2->played++;

        // Update goals for and against
        $standing1->{Standing::GOAL_FOR} += $score1;
        $standing1->{Standing::GOAL_AGAINST} += $score2;
        $standing2->{Standing::GOAL_FOR} += $score2;
        $standing2->{Standing::GOAL_AGAINST} += $score1;

        // Update goal difference
        $standing1->{Standing::GD} = $standing1->{Standing::GOAL_FOR} - $standing1->{Standing::GOAL_AGAINST};
        $standing2->{Standing::GD} = $standing2->{Standing::GOAL_FOR} - $standing2->{Standing::GOAL_AGAINST};

        // Update points, wins, draws, losses
        if ($score1 > $score2) {
            $standing1->{Standing::WIN}++;
            $standing1->{Standing::POINT} += 3;
            $standing2->{Standing::LOSE}++;
        } elseif ($score1 < $score2) {
            $standing2->{Standing::WIN}++;
            $standing2->{Standing::POINT} += 3;
            $standing1->{Standing::LOSE}++;
        } else {
            $standing1->{Standing::DRAW}++;
            $standing2->{Standing::DRAW}++;
            $standing1->{Standing::POINT}++;
            $standing2->{Standing::POINT}++;
        }

        $standing1->save();
        $standing2->save();
    }
}
