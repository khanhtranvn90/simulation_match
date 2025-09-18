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
        Schema::create('standings', function (Blueprint $table) {
            $table->integer('team_id')->unsigned()->primary();
            $table->integer('played')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('draws')->default(0);
            $table->integer('losses')->default(0);
            $table->integer('goals_for')->default(0);
            $table->integer('goals_against')->default(0);
            $table->integer('goal_difference')->default(0);
            $table->integer('points')->default(0);
            $table->foreign('team_id')->references('team_id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standings', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
        });
        Schema::dropIfExists('standings');
    }
};
