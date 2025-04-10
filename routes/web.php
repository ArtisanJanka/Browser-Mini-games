<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FlappyController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', [FlappyController::class, "home"]);
