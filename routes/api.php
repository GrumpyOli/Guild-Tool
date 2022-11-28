<?php

use App\Http\Controllers\LinkedCharactersController;
use App\Http\Controllers\TrackingController;
use App\Http\Middleware\EnsureUserBelongsToGuild;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Managing tracking
Route::get('tracking/{guild_id}/{character_id}', [TrackingController::class, 'retrieve']);
Route::delete('tracking/{guild_id}/{character_id}', [TrackingController::class, 'delete']);
Route::post('tracking', [TrackingController::class, 'store']);

// Managing link between guild and characters
Route::delete('linked_character/{guild_id}/{character_id}', [LinkedCharactersController::class, 'delete']);

Route::middleware( EnsureUserBelongsToGuild::class )->get('test/{guild_id}/{character_id}', function(){
    dd('test');
});

