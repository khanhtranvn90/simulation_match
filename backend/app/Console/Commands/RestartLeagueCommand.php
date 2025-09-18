<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\NullOutput;
use App\Models\MatchModel;
use App\Models\Standing;
use App\Models\Team;
use App\Services\PredictionCacheServiceInterface;

class RestartLeagueCommand extends Command
{
    protected $signature = 'league:restart';
    protected $description = 'Restart league by truncating data and reseeding';

    protected PredictionCacheServiceInterface $predictionCacheService;

    public function __construct(PredictionCacheServiceInterface $predictionCacheService)
    {
        parent::__construct();
        $this->predictionCacheService = $predictionCacheService;
    }

    public function handle(): int
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            MatchModel::query()->delete();
            Standing::query()->delete();
            Team::query()->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            Cache::flush();
            $this->predictionCacheService->clearAllCache();

            Artisan::call('db:seed', [
                '--class' => \Database\Seeders\TeamSeeder::class,
            ], new NullOutput());

            Artisan::call('db:seed', [
                '--class' => \Database\Seeders\StandingSeeder::class,
            ], new NullOutput());

            $this->info('League restarted successfully!');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('League restart failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
