<?php

use App\Models\Tagesbericht;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route("berichtshefte.index");
});

Route::get('/berichtshefte/', function () {

////  Option #1
//  $berichtshefte = Tagesbericht::get()
//    ->merge(Wochenbericht::get())
//    ->sortByDesc('created_at');

//  Option #2
  $berichtshefte = collect()
    ->merge(Tagesbericht::all()->map(function ($bericht) {
      $bericht->type = 'tagesbericht';
      
      return $bericht;
    }))
//    ->merge(Wochenbericht::all())
    ->sortByDesc('sort_date')
    ->values();
  
  
  return view('index', [
      'berichtshefte' => $berichtshefte,
    ]);
})->name("berichtshefte.index");

Route::view('/berichtshefte/neuerTagesbericht', 'create_tagesbericht')->name('create.tagesbericht');

Route::view('/berichtshefte/neuerWochenbericht', 'create_wochenbericht')->name('create.wochenbericht');

Route::get('/berichtshefte/{type}/{id}', function (string $type, int $id) {
  if ($type === 'tagesbericht') {
    $bericht = Tagesbericht::findOrFail($id);
    
    return view('show_tagesbericht', [
      'bericht' => $bericht,
    ]);
  }
  
  abort(404);
})->name('berichtshefte.show');
