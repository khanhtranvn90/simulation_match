<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PredictionService;
use App\Repositories\MatchRepositoryInterface;
use App\Services\PredictionCacheServiceInterface;
use App\Services\MatchServiceInterface;
use Mockery;

class PredictionServiceTest extends TestCase
{
    protected $matchRepoMock;
    protected $cacheMock;
    protected $matchServiceMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->matchRepoMock = Mockery::mock(MatchRepositoryInterface::class);
        $this->cacheMock = Mockery::mock(PredictionCacheServiceInterface::class);
        $this->matchServiceMock = Mockery::mock(MatchServiceInterface::class);
        $this->service = new PredictionService($this->matchServiceMock, $this->matchRepoMock, $this->cacheMock);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function testGetPredictionForLeagueReturnsCached()
    {
        $this->matchRepoMock->shouldReceive('currentWeek')->andReturn(4);
        $this->cacheMock->shouldReceive('hasWeek')->with(4)->andReturn(true);
        $this->cacheMock->shouldReceive('getWeekData')->with(4)->andReturn([['name' => 'A', 'percentage' => 50]]);
        $result = $this->service->getPredictionForLeague();
        $this->assertEquals([['name' => 'A', 'percentage' => 50]], $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function testGetPredictionForLeagueReturnsEmptyIfWeekTooLow()
    {
        $this->matchRepoMock->shouldReceive('currentWeek')->andReturn(2);
        $result = $this->service->getPredictionForLeague();
        $this->assertEmpty($result);
    }
}
