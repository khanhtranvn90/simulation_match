<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('match_id');
            $table->integer('team1_id')->unsigned();
            $table->integer('team2_id')->unsigned();
            $table->integer('team1_score')->nullable();
            $table->integer('team2_score')->nullable();
            $table->integer('week');
            $table->boolean('simulated')->default(false);
            $table->foreign('team1_id')->references('team_id')->on('teams');
            $table->foreign('team2_id')->references('team_id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['team1_id']);
            $table->dropForeign(['team2_id']);
        });
        Schema::dropIfExists('matches');
    }
};
