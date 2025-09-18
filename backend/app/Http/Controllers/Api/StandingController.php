<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StandingServiceInterface;

class StandingController extends Controller
{
    /**
     * @var StandingServiceInterface
     */
    protected $service;

    /**
     * StandingController constructor.
     *
     * @param StandingServiceInterface $service
     */
    public function __construct(StandingServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Get all standings.
     * GET /api/standings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $standings = $this->service->getStandings();
            $result = is_array($standings) && count($standings) && method_exists($standings[0], 'toArray')
                ? array_map(fn($dto) => $dto->toArray(), $standings)
                : $standings;
            return response()->json($result, 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
