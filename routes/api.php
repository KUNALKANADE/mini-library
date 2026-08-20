<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\LoanController;
use Illuminate\Support\Facades\Route;

Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book}', [BookController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/books/{book}/borrow', [BookController::class, 'borrow']);
    Route::post('/loans/{loan}/return', [LoanController::class, 'return']);
    Route::get('/my-loans', [LoanController::class, 'index']);
});
