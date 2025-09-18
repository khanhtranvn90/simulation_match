<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchModel extends Model
{
    CONST TABLE_NAME = 'matches';
    CONST ID = 'match_id';
    CONST TEAM_1 = 'team1_id';
    CONST TEAM_2 = 'team2_id';
    CONST TEAM_1_SCORE = 'team1_score';
    CONST TEAM_2_SCORE = 'team2_score';
    CONST WEEK = 'week';
    CONST SIMULATED = 'simulated';

    protected $table = MatchModel::TABLE_NAME;
    protected $primaryKey = MatchModel::ID;
    public $timestamps = false;

    protected $fillable = [
        MatchModel::TEAM_1,
        MatchModel::TEAM_2,
        MatchModel::TEAM_1_SCORE,
        MatchModel::TEAM_2_SCORE,
        MatchModel::WEEK,
        MatchModel::SIMULATED
    ];

    // Match belongs to Team (team1)
    public function team1()
    {
        return $this->belongsTo(Team::class, MatchModel::TEAM_1, Team::ID);
    }

    // Match belongs to Team (team2)
    public function team2()
    {
        return $this->belongsTo(Team::class, MatchModel::TEAM_2, Team::ID);
    }
}