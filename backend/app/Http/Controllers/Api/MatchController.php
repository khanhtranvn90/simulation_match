<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MatchServiceInterface;

class MatchController extends Controller
{
    /**
     * @var MatchServiceInterface
     */
    protected $matchService;

    /**
     * MatchController constructor.
     *
     * @param MatchServiceInterface $matchService
     */
    public function __construct(MatchServiceInterface $matchService)
    {
        $this->matchService = $matchService;
    }

    /**
     * Get matches for the current week.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function currentWeekMatch()
    {
        try {
            $matches = $this->matchService->currentWeekMatch();
            return response()->json($matches, 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
