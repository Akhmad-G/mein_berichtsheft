<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DayReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WochenberichtController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return view('landing');
});

Route::middleware(['auth', 'verified'])->group(function () {
  Route::get('/kalender', [CalendarController::class, 'index'])->name('calendar');
  Route::put('/kalender/{date}', [DayReportController::class, 'update'])
    ->where('date', '\d{4}-\d{2}-\d{2}')
    ->name('day-reports.update');

//  Route::get('/tagesbericht', [CalendarController::class, 'index'])->name('tagesbericht');
//  Route::put('/tagesbericht/{date}', [CalendarController::class, 'update'])->name('day-reports.update');
});

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
  Route::get('wochenberichte', [WochenberichtController::class, 'index'])
    ->name('weekly-reports.index');
  Route::get('/wochenberichte/{path}', [WochenberichtController::class, 'index'])
    ->name('wochenberichte.show');

});

require __DIR__ . '/auth.php';
