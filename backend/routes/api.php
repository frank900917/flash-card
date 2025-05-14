<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FlashCardSetController;

Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/changePassword', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');

Route::apiResource('/flashCard', FlashCardSetController::class)->middleware('auth:sanctum')->except(['show']);
Route::get('/flashCard/{id}', [FlashCardSetController::class, 'show']);
Route::get('/public', [FlashCardSetController::class, 'publicIndex']);