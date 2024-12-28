<?php

use App\Enums\TournamentStage;
use App\Services\EnumService;
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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->integer('tournament_id');
            $table->integer('tournament_stage');
            $table->integer('player_1_id');
            $table->integer('player_2_id');
            $table->integer('winner_id')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->timestamps();

            $table->foreign('tournament_id')->references('id')->on('tournaments');
            $table->foreign('player_1_id')->references('id')->on('players');
            $table->foreign('player_2_id')->references('id')->on('players');
            $table->foreign('winner_id')->references('id')->on('players');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
