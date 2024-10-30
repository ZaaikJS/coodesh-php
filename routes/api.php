<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DictionaryController;

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

Route::post('/auth/signup', [AuthController::class, 'signup']);
Route::post('/auth/signin', [AuthController::class, 'signin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/entries/{language}', [DictionaryController::class, 'entries']);
    Route::get('/entries/{language}/{word}', [DictionaryController::class, 'show']);
    Route::post('/entries/{language}/{word}/favorite', [DictionaryController::class, 'addFavorite']);
    Route::delete('/entries/{language}/{word}/unfavorite', [DictionaryController::class, 'removeFavorite']);
    Route::get('/user/me', [DictionaryController::class, 'userProfile']);
    Route::get('/user/me/history', [DictionaryController::class, 'userHistory']);
    Route::get('/user/me/favorites', [DictionaryController::class, 'userFavorites']);
});
