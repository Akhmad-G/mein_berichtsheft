<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagesberichtController;
use App\Http\Controllers\WochenberichtController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::middleware(['auth', 'verified'])->group(function () {
  Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::resource('tagesberichte', TagesberichtController::class)->except(['show']);
    Route::resource('wochenberichte', WochenberichtController::class)->except(['show']);
    
    Route::get('/tagesberichte/{path}', [TagesberichtController::class, 'show'])->name('tagesberichte.show');
    
    Route::get('/wochenberichte/{path}/pdf', [WochenberichtController::class, 'pdf'])
        ->name('wochenberichte.pdf');
    
    Route::get('/wochenberichte/{path}', [WochenberichtController::class, 'show'])->name('wochenberichte.show');
    
    Route::get('/wochenberichte-uebernehmen', [WochenberichtController::class, 'uebernehmen'])
        ->name('wochenberichte.uebernehmen');
    
    Route::post('/wochenberichte/{path}/sign', [WochenberichtController::class, 'sign'])->name('wochenberichte.sign');
});

require __DIR__.'/auth.php';
