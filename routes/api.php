<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth
Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::get('/unauthenticated', 'unauthenticated')->name('login');
    Route::post('/register', 'register')->name('auth.register');
    Route::post('/login', 'login')->name('auth.login');
});

// Notes
Route::controller(NoteController::class)
    ->middleware(['auth:api'])
    ->prefix('notes')
    ->group(function () {
        Route::post('/', 'store')->name('notes.store');
        Route::get('/', 'index')->name('notes.index');
        Route::get('/{id}','show')->name('notes.show');
        Route::put('/{id}', 'update')->name('notes.update');
        Route::delete('/{id}', 'destroy')->name('notes.destroy');
});

// Tags
Route::controller(TagController::class)
    ->middleware(['auth:api'])
    ->prefix('tags')
    ->group(function () {
        Route::get('/', 'index');

});
