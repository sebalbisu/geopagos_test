<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'tournament', 'as' => 'tournament.'], function () {
    Route::get('/', [TournamentController::class, 'index'])->name('index');
    Route::post('/', [TournamentController::class, 'create'])->name('create');
    Route::get('/{id}', [TournamentController::class, 'show'])->name('show');
    Route::post('/{id}/play', [TournamentController::class, 'play'])->name('play');
});
