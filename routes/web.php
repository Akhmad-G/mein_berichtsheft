<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WochenberichtController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return view('landing');
});

Route::middleware(['auth', 'verified'])->group(function () {
  Route::get('/kalender', [CalendarController::class, 'index'])->name('kalender');

  Route::get('/tagesbericht', [CalendarController::class, 'index'])->name('tagesbericht');
  Route::put('/tagesbericht/{date}', [CalendarController::class, 'update'])->name('tagesbericht.speichern');
});

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
  Route::get('wochenberichte', [WochenberichtController::class, 'index'])
    ->name('wochenberichte.index');
  Route::get('/wochenberichte/{path}', [WochenberichtController::class, 'index'])
    ->name('wochenberichte.show');

});

require __DIR__ . '/auth.php';
