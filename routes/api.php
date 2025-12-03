<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
use Illuminate\Support\Facades\Route;

Route::apiResource('authors', AuthorController::class);
Route::apiResource('books', BookController::class);

Route::post('borrow', [BorrowController::class, 'borrow']);
Route::post('return/{borrow_id}', [BorrowController::class, 'return']);
Route::get('borrow/history', [BorrowController::class, 'history']);

