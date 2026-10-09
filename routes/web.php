<?php

use App\Http\Controllers\AzubiController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DayReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrainingRecordController;
use App\Http\Controllers\WeeklyReportController;

// start page depends on the role ("Zurück zum Heft") and authentification
Route::get('/', function () {
  return auth()->check()
    ? redirect()->route(auth()->user()->isAusbilder() ? 'weekly-reports.index' : 'calendar')
    : view('landing');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
  Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
  Route::put('/calendar/{date}', [DayReportController::class, 'update'])
    ->where('date', '\d{4}-\d{2}-\d{2}')
    ->name('day-reports.update');

  Route::controller(WeeklyReportController::class)
    ->prefix('/weekly-reports')
    ->name('weekly-reports.')
    ->group(function () {
      Route::get('/', 'index')->name('index');
      Route::get('/{user}/{year}/{week}', 'index')->name('show');
      Route::post('/{user}/{year}/{week}/submit', 'submit')->name('submit');
      Route::post('/{user}/{year}/{week}/sign', 'sign')->name('sign');
      Route::get('/{user}/{year}/{week}/print', 'print')->name('print');
      Route::get('/{user}/{year}/{week}/pdf', 'pdf')->name('pdf');
    })
    ->whereNumber(['year', 'week']);

  Route::get('/azubis', [AzubiController::class, 'index'])->name('azubis.index');
  Route::get('/azubis/{user}', [AzubiController::class, 'index'])->name('azubis.show');

  Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

  // TODO: migrate to the new design (data + password, anchor #password)

//  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

  Route::get('/training-record/{user}/print', [TrainingRecordController::class, 'print'])->name('training-record.print');
});


require __DIR__ . '/auth.php';
