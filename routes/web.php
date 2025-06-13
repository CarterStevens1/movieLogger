<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/my-boards/movies', 'board')->middleware('auth')->name('board');
Route::view('/my-boards/', 'boards')->middleware('auth')->name('boards');


Route::get('boards/create', [BoardController::class, 'create'])->middleware('auth')->name('boards.create');
Route::post('boards/create', [BoardController::class, 'store'])->middleware('auth');

// Check if ID of user is the same as the user_id of the board
Route::get('/my-boards/{board}', [BoardController::class, 'show'])->middleware(['checkUserID', 'auth'])->name('boards.show');


Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create']);
    Route::post('register', [RegisteredUserController::class, 'store']);


    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login');
});


Route::get('edit', [RegisteredUserController::class, 'edit'])->middleware('auth')->name('edit');
Route::post('edit', [RegisteredUserController::class, 'update'])->middleware('auth')->name('update');

Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth');
