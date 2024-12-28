<?php

namespace App\Services;

use App\Enums\Gender;
use App\Enums\SearchFilter;
use App\Enums\SearchSortBy;
use App\Enums\SearchSortOrder;
use App\Enums\TournamentStage;
use App\Models\Game;
use App\Models\Player;
use App\Models\Tournament;
use App\Services\GameService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use App\Exceptions\TournamentException;
use App\Services\TournamentSearch\SearchFilters;
use Illuminate\Contracts\Database\Eloquent\Builder;
use TypeError;

class TournamentService
{
    public function __construct(protected GameService $gameService) {}


    public function create(string $name, Gender $gender, array $playersData = []): Tournament
    {
        $tournament = new Tournament(compact('name', 'gender'));
        $tournament->save();
        $tournament->addPlayers(collect($playersData)
            ->map(function ($playerData) {
                return new Player($playerData);
            }));

        return $tournament;
    }

    public function play(Tournament $tournament): Player
    {
        if (!$tournament->areNumberOfPlayersOk()) {
            throw new TournamentException('bad number of players');
        }

        Log::info('Tournament started', ['tournament' => $tournament->id]);

        $players = $tournament->players->shuffle();
        $stage = log(count($players), 2);

        Log::info('First order players', [
            'tournament' => $tournament->id,
            'players' => $players->pluck('id')->toArray(),
        ]);
        Log::info(
            "Total Stages: $stage",
            ['tournament' => $tournament->id]
        );

        while ($stage > 0) {
            $players = $this->playStage($stage, $players, $tournament);
            $stage--;
        }

        $tournament->winner()->associate($players->first());
        $tournament->save();

        return $tournament->winner;
    }

    protected function playStage(int $stage, Collection $players, Tournament $tournament): Collection
    {
        Log::info("Stage $stage", [
            'tournament' => $tournament->id,
            'players' => $players->pluck('id')->toArray()
        ]);

        $winners = collect();
        foreach ($players->chunk(2) as $gamePlayers) {
            $game = $this->gameService->create($gamePlayers->first(), $gamePlayers->last(), $stage);
            $this->gameService->play($game);
            $winners->push($game->winner);
        }
        return $winners;
    }

    /**
     * @param array $filters
     * @param \App\Enums\SearchSortBy|null $sortBy
     * @param \App\Enums\SearchSortOrder|null $sortOrder
     * @return \Illuminate\Support\Collection
     */
    public function searchBuilder(
        array $filters = [],
        ?SearchSortBy $sortBy = null,
        ?SearchSortOrder $sortOrder = null
    ): Builder {

        return Tournament::query()
            ->when(
                $filters[SearchFilter::Name->value] ?? false,
                fn($query, $value) => $query->where(SearchFilter::Name->value, 'like', "%$value%")
            )
            ->when(
                $filters[SearchFilter::StartedAt->value] ?? false,
                fn($query, $value) => $query->whereDate(SearchFilter::StartedAt->value, $value)
            )
            ->when(
                $filters[SearchFilter::Since->value] ?? false,
                fn($query, $value) => $query->whereDate(SearchFilter::Since->value, '>=', $value)
            )
            ->when(
                $filters[SearchFilter::Until->value] ?? false,
                fn($query, $value) => $query->whereDate(SearchFilter::Until->value, '<=', $value)
            )
            ->when(
                $filters[SearchFilter::Gender->value] ?? false,
                fn($query, $value) => $query->where(SearchFilter::Gender->value, $value)
            )
            ->when(
                $sortBy,
                fn($query, $sortBy) => $query->orderBy($sortBy, $sortOrder ?? SearchSortOrder::Asc)
            );
    }
}
