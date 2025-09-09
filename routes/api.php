<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\StatsController;

Route::post('/tickets', [TicketController::class, 'store']);
Route::get('/tickets', [TicketController::class, 'index']);
Route::get('/tickets/{id}', [TicketController::class, 'show']);
Route::patch('/tickets/{id}', [TicketController::class, 'update']);
Route::post('/tickets/{id}/classify', [TicketController::class, 'classify'])->middleware('throttle:20,1');
Route::get('/stats', [StatsController::class, 'index']);
