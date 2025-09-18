<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\StandingServiceInterface;
use App\Services\StandingService;
use App\Services\LeagueServiceInterface;
use App\Services\LeagueService;
use App\Repositories\MatchRepositoryInterface;;
use App\Repositories\MatchRepository;
use App\Repositories\TeamRepositoryInterface;;
use App\Repositories\TeamRepository;
use App\Repositories\StandingRepositoryInterface;
use App\Repositories\StandingRepository;
use App\Services\MatchServiceInterface;
use App\Services\MatchService;
use App\Services\PredictionCacheServiceInterface;
use App\Services\PredictionCacheService;
use App\Services\PredictionServiceInterface;
use App\Services\PredictionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MatchRepositoryInterface::class, MatchRepository::class);
        $this->app->bind(StandingRepositoryInterface::class, StandingRepository::class);
        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);

        $this->app->bind(StandingServiceInterface::class, StandingService::class);
        $this->app->bind(LeagueServiceInterface::class, LeagueService::class);
        $this->app->bind(MatchServiceInterface::class, MatchService::class);
        $this->app->bind(PredictionCacheServiceInterface::class, PredictionCacheService::class);
        $this->app->bind(PredictionServiceInterface::class, PredictionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
