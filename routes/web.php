<?php

use App\Http\Controllers\DevAndAdminPagesController;
use App\Http\Controllers\LoginController;
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

Route::middleware( \App\Http\Middleware\EnsureBnetAuthenticated::class )->group( function() {

    Route::get('/', function () { return view('dashboard'); })->name('HomePage');
    Route::get('Data', [DevAndAdminPagesController::class, 'Data'])->name('Data');
    
});

Route::get('Login', [LoginController::class, 'beginProcess'])->name('LoginPage');
Route::get('Logout', [LoginController::class, 'Logout'])->name('Logout');

// BattleNet Oauth processing code received after user logged into Blizzard Account
Route::get('Bnet/Oauth/', [LoginController::class, 'processingCode'])->name('BlizzardLandingPage');