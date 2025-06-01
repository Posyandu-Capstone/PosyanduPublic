<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\GoogleAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/google', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/google/call-back', [GoogleAuthController::class, 'callbackGoogle']);

Route::get('/{any}', function () {
    return view('welcome'); 
})->where('any', '.*');


Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset', ['token' => $token]);
})->name('password.reset');