<?php

use Illuminate\Support\Facades\Route;
use App\Services\LeagueService;
use App\Services\MatchService;
use App\Services\PredictionService;
use App\Repositories\TeamRepository;
use App\Repositories\MatchRepository;
use App\Repositories\StandingRepository;

// Route::get('/league/test-simulation', function () {
//     return response()->json(['msg' => '123123']);
// });

Route::get('/league/test-simulation', function() {

    $teamRepo = new TeamRepository();
    $matchRepo = new MatchRepository();
    $standingRepo = new StandingRepository();

    $leagueService = new LeagueService($teamRepo, $matchRepo);
    $standingService = new \App\Services\StandingService($standingRepo, $teamRepo);
    $matchService = new MatchService($matchRepo, $standingService);
    $predictionService = new PredictionService($matchService, $matchRepo);

    $leagueDuration = config('app.league_duration', 5);

    // Simulate and get winning probabilities
    ob_start();
    $predictionService->getPredictionForLeague($leagueDuration, 100000);
    $output = ob_get_clean();

    return response()->json([
        'message' => 'Simulation done',
        'prediction' => $output
    ]);
});