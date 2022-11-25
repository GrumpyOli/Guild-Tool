<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuildController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UpdatingDatabaseController;
use App\Http\Middleware\EnsureGuildIsSelected;
use App\Http\Middleware\EnsureBnetAuthenticated;
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
    Route::get('/', function () { return view('dashboard'); })->name('HomePage');
    Route::get('/roster', [GuildController::class, 'viewRoster'])->name('Roster');
    Route::get('/account', [AccountController::class, 'summary'])->name('Account');
});

Route::middleware( EnsureBnetAuthenticated::class )->group( function (){

    Route::get('/pick-a-guild', [GuildController::class, 'guildSelection'])->name('GuildSelection');
    Route::post('/pick-a-guild', [GuildController::class, 'formSubmitted'])->name('GuildSelection');

    Route::group([ 'prefix' => '/admin', 'as' => 'admin.'], function(){
    
        Route::get('data', [AdminController::class, 'viewData'])->name('data');
        Route::get('fetch_realm_data', [AdminController::class, 'fetch_realm_data'])->name('fetch_realm_data');
        Route::get('current_guild_update', [AdminController::class, 'current_guild_update'])->name('current_guild_update');
        Route::get('current_guild_update_raiderio', [UpdatingDatabaseController::class, 'update_guild_raider_io'])->name('current_guild_update_raiderio');
        Route::match(['GET', 'POST'], 'api_request', [AdminController::class, 'api_request'])->name('api_request');
        Route::redirect('/', 'admin/data');
        
    });
    
});



Route::get('login', [LoginController::class, 'beginProcess'])->name('LoginPage');
Route::get('logout', [LoginController::class, 'Logout'])->name('Logout');

// BattleNet Oauth processing code received after user logged into Blizzard Account
Route::get('Bnet/Oauth/', [LoginController::class, 'processingCode'])->name('BlizzardLandingPage');