<?php
namespace App\Services;

interface PredictionCacheServiceInterface
{
    public function getWeekData(int $week);
    public function setWeekData(int $week, $data);
    public function hasWeek(int $week): bool;
    public function clearAllCache(): void;
}
