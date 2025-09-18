<?php
namespace App\Services;

interface PredictionServiceInterface
{
    /**
     * Get prediction for the league based on current week
     */
    public function getPredictionForLeague(): array;
}
