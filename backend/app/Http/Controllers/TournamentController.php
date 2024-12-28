<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Enums\SearchFilter;
use App\Enums\SearchSortBy;
use App\Enums\SearchSortOrder;
use App\Models\Player;
use App\Models\Tournament;
use App\Services\TournamentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class TournamentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/tournament",
     *     summary="Create a new tournament",
     *     tags={"Tournaments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Summer Tournament"),
     *             @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *             @OA\Property(
     *                 property="players",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="dni", type="integer", example=12345678),
     *                     @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *                     @OA\Property(property="first_name", type="string", example="John"),
     *                     @OA\Property(property="last_name", type="string", example="Doe"),
     *                     @OA\Property(property="age", type="integer", example=25),
     *                     @OA\Property(property="skill", type="number", example=85.5),
     *                     @OA\Property(property="strength", type="number", example=75.0),
     *                     @OA\Property(property="speed", type="number", example=80.0),
     *                     @OA\Property(property="latency", type="number", example=50.0),
     *                     @OA\Property(property="experience", type="number", example=5.0),
     *                 ),
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="dni", type="integer", example=432543),
     *                     @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *                     @OA\Property(property="first_name", type="string", example="Qewr"),
     *                     @OA\Property(property="last_name", type="string", example="Asdf"),
     *                     @OA\Property(property="age", type="integer", example=44),
     *                     @OA\Property(property="skill", type="number", example=22.5),
     *                     @OA\Property(property="strength", type="number", example=33.0),
     *                     @OA\Property(property="speed", type="number", example=2.0),
     *                     @OA\Property(property="latency", type="number", example=99.0),
     *                     @OA\Property(property="experience", type="number", example=10.0),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tournament created successfully",
     *          @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *          @OA\JsonContent()
     *     )
     * )
     */
    public function create(Request $request, TournamentService $tournamentService)
    {
        $data = collect($request->validate([
            'name' => 'required|string',
            'gender' => ['required', Rule::enum(Gender::class)],
            'players' => 'required|array|min:2',
            'players.*.dni' => 'required|integer',
            'players.*.gender' => 'required|same:gender',
            'players.*.first_name' => 'required|string',
            'players.*.last_name' => 'required|string',
            'players.*.age' => 'required|integer|min:18|max:60',
            'players.*.skill' => 'required|numeric',
            'players.*.strength' => 'nullable|numeric',
            'players.*.speed' => 'nullable|numeric',
            'players.*.latency' => 'nullable|numeric',
        ]));

        $tournament = $tournamentService->create(
            $data->get('name'),
            Gender::from($data->get('gender')),
            $data->get('players')
        );

        return response($tournament, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/tournament",
     *     summary="Get a list of tournaments",
     *     tags={"Tournaments"},
     *     @OA\Parameter(
     *         name="filters",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="type", type="string", description="Filter type"),
     *                 @OA\Property(property="value", type="string", description="Filter value")
     *             )
     *         ),
     *         description="Array of filters"
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Sort by field"
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Sort order"
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1),
     *         description="Page number"
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100),
     *         description="Number of items per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function index(Request $request, TournamentService $tournamentService)
    {
        $data = collect($request->validate([
            'filters' => 'sometimes|array',
            'filters.*.type' => ['required', Rule::enum(SearchFilter::class)],
            'filters.*.value' => 'required|string',
            'sort_by' => ['sometimes', Rule::enum(SearchSortBy::class)],
            'sort_order' => ['sometimes', Rule::enum(SearchSortOrder::class)],
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]));

        $filters = collect($data->get('filters', []))
            ->mapWithKeys(fn($filter) => [$filter['type'] => $filter['value']])
            ->toArray();

        $tournaments = $tournamentService->searchBuilder(
            $filters,
            $data->get('sort_by', null),
            $data->get('sort_order', null)
        )->paginate($data->get('per_page', 10));

        return response($tournaments);
    }

    /**
     * @OA\Post(
     *     path="/api/tournament/{id}/play",
     *     summary="play a tournament",
     *     tags={"Tournaments"},
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Player Winner",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function play(Request $request, TournamentService $tournamentService, int $id)
    {
        $tournament = Tournament::findOrFail($id);

        $winner = $tournamentService->play($tournament);

        return response($winner);
    }

    /**
     * @OA\Get(
     *     path="/api/tournament/{id}",
     *     summary="show a tournament",
     *     tags={"Tournaments"},
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     * 
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Tournament",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function show(int $id)
    {
        $tournament = Tournament::with('players')->findOrFail($id);

        return response($tournament);
    }
}
