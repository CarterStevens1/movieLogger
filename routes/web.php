<?php

use App\Http\Controllers\BoardCellsController;
use App\Http\Controllers\BoardColumnsController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\BoardRowsController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');


Route::get('my-boards', [BoardController::class, 'index'])->middleware('auth')->name('boards');


Route::get('boards/create', [BoardController::class, 'create'])->middleware('auth')->name('boards.create');
Route::post('boards/create', [BoardController::class, 'store'])->middleware('auth');



Route::middleware(['auth'])->group(function () {
    // Existing column routes
    Route::post('/board-columns', [BoardColumnsController::class, 'store']);
    Route::patch('/board-columns/{column}/sort', [BoardColumnsController::class, 'updateSort']);
    Route::patch('/board-columns/reorder', [BoardColumnsController::class, 'reorder']);

    // New row routes
    Route::post('/board-rows', [BoardRowsController::class, 'store']);
    Route::patch('/board-rows/reorder', [BoardRowsController::class, 'reorder']);
    Route::delete('/board-rows/{row}', [BoardRowsController::class, 'destroy']);

    Route::post('/board-cells', [BoardCellsController::class, 'store']);
    Route::patch('/board-cells/{cell}', [BoardCellsController::class, 'update']);
    Route::post('/board-cells/bulk', [BoardCellsController::class, 'bulkStore']);

    Route::post('/update-cell-values', [BoardController::class, 'updateCellValues']);
});

// Check if ID of user is the same as the user_id of the board
Route::get('/my-boards/{board}', [BoardController::class, 'show'])->middleware(['checkUserID', 'auth'])->name('boards.show');
Route::get('/my-boards/{board}/edit', [BoardController::class, 'edit'])->middleware(['checkUserID', 'auth'])->name('boards.edit');
Route::post('/my-boards/{board}/edit', [BoardController::class, 'update'])->middleware(['checkUserID', 'auth'])->name('boards.update');
Route::post('/my-boards/{board}/delete', [BoardController::class, 'destroy'])->middleware(['checkUserID', 'auth'])->name('boards.destroy');
Route::post('/my-boards/{board}/share', [BoardController::class, 'share'])->middleware(['checkUserID', 'auth'])->name('boards.share');
Route::post('/my-boards/{board}/unshare', [BoardController::class, 'unshare'])->middleware(['checkUserID', 'auth'])->name('boards.unshare');




Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->name('register');


    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login');
});


Route::get('edit', [RegisteredUserController::class, 'edit'])->middleware('auth')->name('edit');
Route::post('edit', [RegisteredUserController::class, 'update'])->middleware('auth')->name('update');
Route::post('destroy', [RegisteredUserController::class, 'destroy'])->middleware('auth')->name('destroy');


Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
