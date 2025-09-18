<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    CONST TABLE_NAME = 'teams';
    CONST ID = 'team_id';
    CONST NAME = 'name';
    CONST STRENGTH = 'strength';

    protected $table = Team::TABLE_NAME;
    protected $primaryKey = Team::ID;

    public $timestamps = false;

    protected $fillable = [
        Team::NAME,
        Team::STRENGTH
    ];

    public function standing()
    {
        return $this->hasOne(Standing::class, Standing::TEAM_ID, Team::ID);
    }

    public function matchesAsTeam1()
    {
        return $this->hasMany(MatchModel::class, MatchModel::TEAM_1, Team::ID);
    }

    public function matchesAsTeam2()
    {
        return $this->hasMany(MatchModel::class, MatchModel::TEAM_2, Team::ID);
    }
}
