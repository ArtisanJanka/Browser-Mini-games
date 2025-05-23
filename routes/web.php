<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FlappyController;
use App\Http\Controllers\TypingGameController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/flappy', [FlappyController::class, 'flappybird'])->name('flappybird');

    Route::get('/play/{difficulty}', [TypingGameController::class, 'play'])->name('play');
    Route::post('/submit', [TypingGameController::class, 'submit'])->name('submit');
    Route::get('/leaderboard', [TypingGameController::class, 'leaderboard'])->name('typing.leaderboard');
    Route::post('/store', [FlappyController::class, 'store'])->name('store.score');
    Route::get('/flappy-leaderboard', [FlappyController::class, 'leaderboard'])->name('flappy.leaderboard');
});

require __DIR__.'/auth.php';
Route::get('/home', [FlappyController::class, "home"]);
Route::get('/flappybird', [FlappyController::class, "flappybird"]);

