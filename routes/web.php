<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuildController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UpdatingDatabaseController;
use App\Http\Middleware\EnsureGuildIsSelected;
use App\Http\Middleware\EnsureBnetAuthenticated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware( EnsureBnetAuthenticated::class, EnsureGuildIsSelected::class )->group( function(){

    Route::group([ 'prefix' => '/guild', 'as' => 'guild.'], function(){

        // View the guild roster
        Route::get('roster', [GuildController::class, 'viewRoster'])->name('roster');

        // View all linked characters to the guild
        Route::get('linked_characters', [GuildController::class, 'viewLinkedCharacters'])->name('linked_characters');

        // View the guild setting page (Ranks..)
        Route::get('settings', [GuildController::class, 'settings'])->name('settings');


        // Forms & Submission
        // Form submitted to update the guild ranks
        Route::post('settings', [GuildController::class, 'settings'])->name('update_rank');

        // Form submitted to add a linked characters
        Route::post('linked_characters',[GuildController::class, 'addLinkedCharacter']);


        // View tracked characters

        // Change the value of tracking a specifid characters
        Route::get('tracking/{character}', [TrackingController::class, 'change'])->name('change_tracking');

        Route::post('tracking/', [TrackingController::class, 'store'])->name('tracking');

        // Guilds note
        Route::get('notes', function() { return view('notes'); })->name('notes');
        Route::get('notes/add', function() { return view('notes_add'); })->name('notes_add');
        Route::post('notes/add', [NotesController::class, 'store']);


    });

    Route::get('/', function () { return view('dashboard'); })->name('HomePage');

    // Account routes
    Route::group([ 'prefix' => '/account', 'as' => 'account.'], function(){

        // Account summary page
        Route::get('/', [AccountController::class, 'summary'])->name('summary');

        // Controller to update and import information from blizzard about account profil
        Route::get('account/update', [AccountController::class, 'updateCharacters'])->name('update');

    });

});

Route::middleware( EnsureBnetAuthenticated::class )->group( function (){

    Route::get('/pick-a-guild', [GuildController::class, 'guildSelection'])->name('GuildSelection');
    Route::post('/pick-a-guild', [GuildController::class, 'formSubmitted'])->name('GuildSelection');

    Route::group([ 'prefix' => '/admin', 'as' => 'admin.'], function(){
    
        // Admin home page (Data view for the moment)
        Route::get('data', [AdminController::class, 'viewData'])->name('data');

        // Populate database for realms
        Route::get('fetch_realm_data', [AdminController::class, 'fetch_realm_data'])->name('fetch_realm_data');

        // Route to update the current guild (Infos, Members, Dungeons)
        Route::get('current_guild_update', [AdminController::class, 'current_guild_update'])->name('current_guild_update');
        Route::get('current_guild_update_raiderio/{GuildID}', [UpdatingDatabaseController::class, 'update_guild_raider_io'])->name('current_guild_update_raiderio');

        // Custom API page
        Route::match(['GET', 'POST'], 'api_request', [AdminController::class, 'api_request'])->name('api_request');

        // Populate Database (Races, Classes, Realms ..)
        Route::get('populate_database', [AdminController::class, 'populate_database'])->name('populate_database');
        
        // Redirecting home page to data page
        Route::redirect('/', 'admin/data');
        
    });
    
});



Route::get('login', [LoginController::class, 'beginProcess'])->name('LoginPage');
Route::get('logout', [LoginController::class, 'Logout'])->name('Logout');

// BattleNet Oauth processing code received after user logged into Blizzard Account
Route::get('BattleNetOauth', [LoginController::class, 'processingCode'])->name('BlizzardLandingPage');