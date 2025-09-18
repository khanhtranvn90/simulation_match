<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\StandingService;
use App\Repositories\StandingRepositoryInterface;
use App\Repositories\TeamRepositoryInterface;
use App\DTO\StandingDTO;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;

class StandingServiceTest extends TestCase
{
    protected $repoMock;
    protected $teamRepoMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoMock = Mockery::mock(StandingRepositoryInterface::class);
        $this->teamRepoMock = Mockery::mock(TeamRepositoryInterface::class);
        $this->service = new StandingService($this->repoMock, $this->teamRepoMock);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function testGetStandingsReturnsCorrectData()
    {
        $standings = [
            [
                'team_id' => 1,
                'played' => 5,
                'wins' => 3,
                'draws' => 1,
                'losses' => 1,
                'goals_for' => 10,
                'goals_against' => 5,
                'goal_difference' => 5,
                'points' => 10
            ],
            [
                'team_id' => 2,
                'played' => 5,
                'wins' => 2,
                'draws' => 2,
                'losses' => 1,
                'goals_for' => 8,
                'goals_against' => 6,
                'goal_difference' => 2,
                'points' => 8
            ]
        ];
        $teams = [
            1 => ['id' => 1, 'name' => 'Chelsea'],
            2 => ['id' => 2, 'name' => 'Arsenal']
        ];
        $this->repoMock->shouldReceive('all')->once()->andReturn($standings);
        $this->teamRepoMock->shouldReceive('all')->once()->andReturn(EloquentCollection::make($teams));
        $result = $this->service->getStandings();
        $this->assertIsArray($result);
        $this->assertInstanceOf(StandingDTO::class, $result[0]);
        $this->assertEquals(10, $result[0]->points);
        $this->assertEquals(5, $result[0]->goal_difference);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function testGetStandingsReturnsEmptyArray()
    {
        $this->repoMock->shouldReceive('all')->once()->andReturn([]);
        $this->teamRepoMock->shouldReceive('all')->once()->andReturn(EloquentCollection::make([]));
        $result = $this->service->getStandings();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
