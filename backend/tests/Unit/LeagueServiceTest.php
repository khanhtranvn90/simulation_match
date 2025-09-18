<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\DB;
use App\Services\LeagueService;
use App\Repositories\StandingRepositoryInterface;
use App\Repositories\TeamRepositoryInterface;
use App\Repositories\MatchRepositoryInterface;
use App\Models\Standing;
use App\Models\Team;
use App\Models\MatchModel;
use App\DTO\ChampionDTO;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class LeagueServiceTest extends TestCase
{
    protected $standingRepo;
    protected $teamRepo;
    protected $matchRepo;
    protected LeagueService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->standingRepo = Mockery::mock(StandingRepositoryInterface::class);
        $this->teamRepo     = Mockery::mock(TeamRepositoryInterface::class);
        $this->matchRepo    = Mockery::mock(MatchRepositoryInterface::class);

        $this->service = new LeagueService(
            $this->standingRepo,
            $this->teamRepo,
            $this->matchRepo
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_generates_round_robin_schedule()
    {
        $teams = EloquentCollection::make([
            (object)[Team::ID => 1],
            (object)[Team::ID => 2],
            (object)[Team::ID => 3],
            (object)[Team::ID => 4],
        ]);

        $this->teamRepo
            ->shouldReceive('all')
            ->once()
            ->andReturn($teams);

        $result = $this->service->generateRoundRobin(3);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey(MatchModel::TEAM_1, $result[0]);
        $this->assertArrayHasKey(MatchModel::TEAM_2, $result[0]);
        $this->assertArrayHasKey(MatchModel::WEEK, $result[0]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_champion_when_single_candidate()
    {
        $this->standingRepo
            ->shouldReceive('max')
            ->with(Standing::POINT)
            ->andReturn(10);

        $team = (object)[ Team::NAME => 'Team A' ];
        $standing = (object)[
            Standing::POINT  => 10,
            Standing::GD     => 5,
            Standing::WIN    => 3,
            Standing::LOSE   => 1,
            Standing::DRAW   => 0,
            Standing::PLAYED => 4,
            'team'           => $team,
        ];

        $this->standingRepo
            ->shouldReceive('getByCriteria')
            ->with([Standing::POINT => 10], ['team'])
            ->andReturn(EloquentCollection::make([$standing]));

        $champion = $this->service->getLeagueChampion();

        $this->assertInstanceOf(ChampionDTO::class, $champion);
        $this->assertEquals('Team A', $champion->toArray()['name']);
        $this->assertEquals(10, $champion->toArray()['pts']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_resolves_tie_by_goal_difference()
    {
        $this->standingRepo
            ->shouldReceive('max')
            ->with(Standing::POINT)
            ->andReturn(10);

        $teamA = (object)[ Team::NAME => 'Team A' ];
        $teamB = (object)[ Team::NAME => 'Team B' ];

        $sA = (object)[
            Standing::POINT  => 10,
            Standing::GD     => 3,
            Standing::WIN    => 3,
            Standing::LOSE   => 1,
            Standing::DRAW   => 0,
            Standing::PLAYED => 4,
            'team'           => $teamA,
        ];
        $sB = (object)[
            Standing::POINT  => 10,
            Standing::GD     => 6, // higher GD
            Standing::WIN    => 3,
            Standing::LOSE   => 1,
            Standing::DRAW   => 0,
            Standing::PLAYED => 4,
            'team'           => $teamB,
        ];

        $this->standingRepo
            ->shouldReceive('getByCriteria')
            ->with([Standing::POINT => 10], ['team'])
            ->andReturn(EloquentCollection::make([$sA, $sB]));

        $champion = $this->service->getLeagueChampion();

        $this->assertEquals('Team B', $champion->toArray()['name']);
    }
}
