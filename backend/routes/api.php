<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\StandingController;
use App\Http\Controllers\Api\SimulationController;

Route::middleware('api')->group(function () {

    // Matches API
    Route::get('matches', [MatchController::class, 'currentWeekMatch']);

    // Standings API
    Route::get('standings', [StandingController::class, 'index']);

    // Simulation API
    Route::get('simulation/next', [SimulationController::class, 'nextWeekMatch']);
    Route::get('simulation/prediction', [SimulationController::class, 'predictionLeague']);
    Route::get('simulation/champion', [SimulationController::class, 'getLeagueChampion']);
    Route::post('simulation/new', [SimulationController::class, 'restartLeague']);

});