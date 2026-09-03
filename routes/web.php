<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagesberichtController;
use App\Http\Controllers\WochenberichtController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'ausbildung.complete'])->group(function () {
  Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
});

Route::middleware(['auth', 'ausbildung.complete'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'ausbildung.complete'])->group(function () {
    Route::resource('tagesberichte', TagesberichtController::class)->except(['show', 'edit']);
    Route::resource('wochenberichte', WochenberichtController::class)->except(['show', 'edit']);
    
    Route::get('/tagesberichte/{path}', [TagesberichtController::class, 'show'])->name('tagesberichte.show');
    Route::get('/wochenberichte/{path}', [WochenberichtController::class, 'show'])->name('wochenberichte.show');
    
    Route::get('/wochenberichte-uebernehmen', [WochenberichtController::class, 'uebernehmen'])
      ->name('wochenberichte.uebernehmen');
});

// temporarily, delete later

Route::get('/debug/wochenbericht', function () {
  $user = auth()->user();
  $service = app(\App\Contracts\GitLabServiceInterface::class);
  $weekStart = \Carbon\Carbon::now()->startOfWeek();
  
  dd($service->getReportsForWeek($user, $weekStart));
});

require __DIR__.'/auth.php';
