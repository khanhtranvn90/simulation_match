<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\MatchModel;
use App\Models\Team;
use App\Models\Standing;
use App\Services\MatchServiceInterface;
use App\Services\PredictionServiceInterface;
use App\Services\PredictionCacheServiceInterface;
use App\Services\LeagueServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SimulationController extends Controller
{
    /**
     * @var MatchServiceInterface
     */
    protected $matchService;

    /**
     * @var PredictionServiceInterface
     */
    protected $predictionService;

    /**
     * @var LeagueServiceInterface
     */
    protected $leagueService;

    /**
     * @var PredictionCacheServiceInterface
     */
    protected $predictionCacheService;

    /**
     * SimulationController constructor.
     *
     * @param MatchServiceInterface $matchService
     * @param PredictionServiceInterface $predictionService
     * @param LeagueServiceInterface $leagueService
     * @param PredictionCacheServiceInterface $predictionCacheService
     */
    public function __construct(
        MatchServiceInterface $matchService,
        PredictionServiceInterface $predictionService,
        LeagueServiceInterface $leagueService,
        PredictionCacheServiceInterface $predictionCacheService
    ) {
        $this->matchService = $matchService;
        $this->predictionService = $predictionService;
        $this->leagueService = $leagueService;
        $this->predictionCacheService = $predictionCacheService;
    }

    /**
     * Simulate next week match.
     * GET /api/simulation/next
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function nextWeekMatch()
    {
        try {
            $this->matchService->nextWeekMatch();
            return response()->json([], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get league predictions.
     * GET /api/simulation/prediction
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function predictionLeague()
    {
        try {
            $predictions = $this->predictionService->getPredictionForLeague();
            return response()->json($predictions, 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get league champion.
     * GET /api/simulation/champion
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLeagueChampion()
    {
        try {
            $champion = $this->leagueService->getLeagueChampion();
            return response()->json($champion ? $champion->toArray() : [], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Restart the league.
     * POST /api/simulation/new
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restartLeague()
    {
        try {
            Artisan::call('league:restart');
            return response()->json(['message' => 'League restarted successfully.'], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'League restart failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

