<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

use Illuminate\Auth\Events\Login;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth')->group(function(){
    Route::get('/getReg',[RegisterController::class,'index'])->name('index.register');
    Route::post('/reg',[RegisterController::class,'register'])->name('register');
    Route::get('/getLog',[LoginController::class,'index'])->name('index.login');
    Route::post('/log',[LoginController::class,'login'])->name('login');

    Route::middleware(['jwt'])->group(function () {
        Route::delete('/logout', [LogoutController::class, 'logout'])->name('logout');
    });

}); 

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');