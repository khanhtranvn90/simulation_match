<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Services\MatchService;
use App\Repositories\MatchRepositoryInterface;
use App\Services\StandingServiceInterface;
use App\Models\MatchModel;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class MatchServiceTest extends TestCase
{
    protected $matchRepo;
    protected $standingService;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->matchRepo = Mockery::mock(MatchRepositoryInterface::class);
        $this->standingService = Mockery::mock(StandingServiceInterface::class);

        $this->service = new MatchService($this->matchRepo, $this->standingService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_simulates_week_normal_case()
    {
        $team1 = (object)[Team::ID => 1, Team::NAME => 'A', Team::STRENGTH => 10];
        $team2 = (object)[Team::ID => 2, Team::NAME => 'B', Team::STRENGTH => 5];

        $match = Mockery::mock(MatchModel::class)->makePartial();
        $match->team1 = $team1;
        $match->team2 = $team2;
        $match->shouldReceive('save')->once();

        $this->matchRepo
            ->shouldReceive('getMatchByWeek')
            ->with(1)
            ->andReturn(new \Illuminate\Database\Eloquent\Collection([$match]));

        $this->standingService
            ->shouldReceive('updateStandings')
            ->with(1, 2, Mockery::type('int'), Mockery::type('int'))
            ->once();

        $this->service->simulateWeek(1);

        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_empty_week()
    {
        $this->matchRepo
            ->shouldReceive('getMatchByWeek')
            ->with(99)
            ->andReturn(new EloquentCollection([]));

        $this->service->simulateWeek(99);
        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function simulate_match_returns_scores()
    {
        $team1 = ['name' => 'A', 'strength' => 10, 'id' => 1];
        $team2 = ['name' => 'B', 'strength' => 5, 'id' => 2];

        $result = $this->service->simulateMatch($team1, $team2);

        $this->assertArrayHasKey(MatchModel::TEAM_1_SCORE, $result);
        $this->assertArrayHasKey(MatchModel::TEAM_2_SCORE, $result);
        $this->assertIsInt($result[MatchModel::TEAM_1_SCORE]);
        $this->assertIsInt($result[MatchModel::TEAM_2_SCORE]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function current_week_match_returns_dtos()
    {
        $matches = [
            [
                MatchModel::ID => 1,
                'team1' => [Team::NAME => 'A'],
                'team2' => [Team::NAME => 'B'],
                MatchModel::TEAM_1_SCORE => 2,
                MatchModel::TEAM_2_SCORE => 1,
                MatchModel::WEEK => 1,
            ]
        ];

        $this->matchRepo
            ->shouldReceive('currentWeekMatch')
            ->andReturn($matches);

        $dtos = $this->service->currentWeekMatch();
        $this->assertCount(1, $dtos);
        $this->assertEquals('A', $dtos[0]->home);
        $this->assertEquals('B', $dtos[0]->away);
    }
}
