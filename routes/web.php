<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/documents/{document}/preview', [App\Http\Controllers\DocumentController::class, 'preview'])
    ->middleware(['auth'])
    ->name('documents.preview');

require __DIR__ . '/auth.php';
