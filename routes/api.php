<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware(['auth:api']);
    Route::get('logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware(['auth:api']);
    Route::post('refresh', [App\Http\Controllers\AuthController::class, 'refresh'])->middleware(['auth:api']);
    Route::get('refresh', [App\Http\Controllers\AuthController::class, 'refresh'])->middleware(['auth:api']);
    Route::post('me', [App\Http\Controllers\AuthController::class, 'me'])->middleware(['auth:api']);
    Route::get('me', [App\Http\Controllers\AuthController::class, 'me'])->middleware(['auth:api']);
    Route::post('register', [App\Http\Controllers\AuthController::class, 'register']);
});

Route::group([
    'middleware' => ['auth:api', 'authadmin'],
    'prefix' => 'user'
], function ($router) {
    Route::get('/', [App\Http\Controllers\UserController::class, 'index']);
    Route::post('/', [App\Http\Controllers\UserController::class, 'add']);
    Route::get('/{id}', [App\Http\Controllers\UserController::class, 'show']);
    Route::post('/{id}', [App\Http\Controllers\UserController::class, 'edit']);
    Route::delete('/delete/{id}', [App\Http\Controllers\UserController::class, 'delete']);
});

Route::group([
    'middleware' => ['auth:api', 'authwriter'],
    'prefix' => 'book'
], function ($router) {
    Route::get('/', [App\Http\Controllers\BookController::class, 'index']);
    Route::post('/', [App\Http\Controllers\BookController::class, 'add']);
    Route::post('/edit/{id}', [App\Http\Controllers\BookController::class, 'edit']);
    Route::get('/delete/{id}', [App\Http\Controllers\BookController::class, 'delete']);
});


