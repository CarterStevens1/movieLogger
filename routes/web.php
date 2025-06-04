<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');


Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create']);
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [LoginController::class, 'create']);
    Route::post('login', [LoginController::class, 'store']);
});



Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth');
