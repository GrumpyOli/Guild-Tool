<?php

use App\Http\Controllers\adminCommands;
use App\Http\Controllers\GuildController;
use App\Http\Controllers\LoginController;
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
    Route::get('/Roster', [GuildController::class, 'viewRoster'])->name('Roster');
    Route::get('/Account', function(){ return 'Account'; })->name('Account');
});

Route::middleware( EnsureBnetAuthenticated::class )->group( function (){

    Route::get('/GuildSelect', [GuildController::class, 'guildSelection'])->name('GuildSelection');
    Route::post('/GuildSelect', [GuildController::class, 'formSubmitted'])->name('GuildSelection');
    
});

Route::group([ 'prefix' => '/admin', 'as' => 'admin.'], function(){
    
    Route::get('data', [adminCommands::class, 'viewData'])->name('data');
    Route::get('fetch_realm_data', [adminCommands::class, 'fetch_realm_data'])->name('fetch_realm_data');
    Route::match(['GET', 'POST'], 'api_request', [adminCommands::class, 'api_request'])->name('api_request');
    Route::redirect('/', 'admin/data');
    
});

Route::get('Login', [LoginController::class, 'beginProcess'])->name('LoginPage');
Route::get('Logout', [LoginController::class, 'Logout'])->name('Logout');

// BattleNet Oauth processing code received after user logged into Blizzard Account
Route::get('Bnet/Oauth/', [LoginController::class, 'processingCode'])->name('BlizzardLandingPage');