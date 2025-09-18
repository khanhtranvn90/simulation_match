<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use App\Models\Team;

class TeamSeeder extends Seeder
{
	public function run(): void
	{
        $teams = Team::all();
        if ($teams->count() >= 4) {
            return;
        }

		Team::create(['name' => 'Manchester United', 'strength' => 90]);
		Team::create(['name' => 'Chelsea', 'strength' => 85]);
		Team::create(['name' => 'Liverpool', 'strength' => 88]);
		Team::create(['name' => 'Arsenal', 'strength' => 84]);

        Artisan::call('league:generate-schedule');
	}
}
