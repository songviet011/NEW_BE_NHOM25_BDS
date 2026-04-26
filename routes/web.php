<?php

use App\Http\Controllers\GiaoDichController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Only return URL in web.php if needed for browser redirects
Route::get('/payment/sepay-return', [GiaoDichController::class, 'handleSePayReturn']);

// routes/web.php
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/unmatched-payments', [\App\Http\Controllers\Admin\UnmatchedPaymentController::class, 'index'])->name('admin.unmatched.index');
    Route::post('/unmatched-payments/{id}/match', [\App\Http\Controllers\Admin\UnmatchedPaymentController::class, 'match'])->name('admin.unmatched.match');
    Route::post('/unmatched-payments/{id}/ignore', [\App\Http\Controllers\Admin\UnmatchedPaymentController::class, 'ignore'])->name('admin.unmatched.ignore');
});