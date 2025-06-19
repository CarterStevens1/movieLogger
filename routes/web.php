<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');


Route::get('my-boards', [BoardController::class, 'index'])->middleware('auth')->name('boards');


Route::get('boards/create', [BoardController::class, 'create'])->middleware('auth')->name('boards.create');
Route::post('boards/create', [BoardController::class, 'store'])->middleware('auth');

// Check if ID of user is the same as the user_id of the board
Route::get('/my-boards/{board}', [BoardController::class, 'show'])->middleware(['checkUserID', 'auth'])->name('boards.show');
Route::get('/my-boards/{board}/edit', [BoardController::class, 'edit'])->middleware(['checkUserID', 'auth'])->name('boards.edit');
Route::post('/my-boards/{board}/edit', [BoardController::class, 'update'])->middleware(['checkUserID', 'auth'])->name('boards.update');
Route::post('/my-boards/{board}/delete', [BoardController::class, 'destroy'])->middleware(['checkUserID', 'auth'])->name('boards.destroy');
Route::post('/my-boards/{board}/share', [BoardController::class, 'share'])->middleware(['checkUserID', 'auth'])->name('boards.share');




Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create']);
    Route::post('register', [RegisteredUserController::class, 'store']);


    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login');
});


Route::get('edit', [RegisteredUserController::class, 'edit'])->middleware('auth')->name('edit');
Route::post('edit', [RegisteredUserController::class, 'update'])->middleware('auth')->name('update');
Route::post('destroy', [RegisteredUserController::class, 'destroy'])->middleware('auth')->name('destroy');


Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth');
