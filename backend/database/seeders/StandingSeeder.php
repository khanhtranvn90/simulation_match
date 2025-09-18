<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Standing;

class StandingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = Team::all();
        $standings = Standing::all();

        if ($standings->count() >= 4) {
            return;
        }

        foreach ($teams as $team) {
            Standing::firstOrCreate([
                Standing::TEAM_ID        => $team->{Team::ID},
                Standing::PLAYED         => 0,
                Standing::WIN           => 0,
                Standing::DRAW          => 0,
                Standing::LOSE         => 0,
                Standing::GOAL_FOR      => 0,
                Standing::GOAL_AGAINST  => 0,
                Standing::POINT         => 0,
            ]);
        }
    }
}
