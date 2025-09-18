<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Standing extends Model
{
    CONST TABLE_NAME = 'standings';
    CONST ID = 'team_id';
    CONST TEAM_ID = 'team_id';
    CONST PLAYED = 'played';
    CONST WIN = 'wins';
    CONST LOSE = 'losses';
    CONST DRAW = 'draws';
    CONST GOAL_FOR = 'goals_for';
    CONST GOAL_AGAINST = 'goals_against';
    CONST GD = 'goal_difference';
    CONST POINT = 'points';

    protected $table = Standing::TABLE_NAME;
    protected $primaryKey = Standing::ID;
    public $timestamps = false;

    protected $fillable = [
        Standing::TEAM_ID,
        Standing::POINT,
        Standing::WIN,
        Standing::LOSE,
        Standing::DRAW,
        Standing::GOAL_FOR,
        Standing::GOAL_AGAINST,
        Standing::PLAYED
    ];

    // Standing belongs to Team
    public function team()
    {
        return $this->belongsTo(Team::class, Standing::TEAM_ID, Team::ID);
    }
}
