<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class PredictionCacheService implements PredictionCacheServiceInterface
{
    protected $ttl = 3600;

    public function getWeekData(int $week)
    {
        $key = $this->getKey($week);
        return Cache::store('redis')->get($key);
    }

    public function setWeekData(int $week, $data)
    {
        $key = $this->getKey($week);
        Cache::store('redis')->put($key, $data, $this->ttl);
    }

    public function hasWeek(int $week): bool
    {
        $key = $this->getKey($week);
        return Cache::store('redis')->has($key);
    }

    protected function getKey(int $week): string
    {
        return "prediction:week:{$week}";
    }

    public function clearAllCache(): void
    {
        Cache::store('redis')->flush();
    }
}